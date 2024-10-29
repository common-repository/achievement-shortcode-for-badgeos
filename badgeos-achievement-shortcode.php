<?php
/**
 * Plugin Name: Achievement Shortcode Add-On for BadgeOS
 * Plugin URI: https://wordpress.org/plugins/achievement-shortcode-for-badgeos/
 * Description:This BadgeOS Add-on adds a shortcode to show or hide content depending on the user having earned a specific achievement.
 * Tags: badgeos, restrict, shortcode
 * Author: konnektiv
 * Version: 1.1.0
 * Requires at least: 3.6.0
 * Requires PHP: 5.5.9
 * Author URI: https://konnektiv.de/
 * License: GNU AGPLv3
 * Text Domain: achievement-shortcode-for-badgeos
 */
/*
 * Copyright © 2012 LearningTimes, LLC; Konnektiv
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General
 * Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/agpl-3.0.html>;.
*/

class BadgeOS_Achievement_Shortcode {

	function __construct() {

		// Define plugin constants
		$this->basename       = plugin_basename( __FILE__ );
		$this->directory_path = plugin_dir_path( __FILE__ );
		$this->directory_url  = plugin_dir_url(  __FILE__ );

		// Load translations
		load_plugin_textdomain( 'achievement-shortcode-for-badgeos', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// If BadgeOS is unavailable, deactivate our plugin
		add_action( 'admin_notices', array( $this, 'maybe_disable_plugin' ) );
		add_action( 'plugins_loaded', array( $this, 'actions' ) );

	}

	public function actions() {
		if ( $this->meets_requirements() ) {
			add_action( 'init', array( $this, 'register_badgeos_shortcodes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 99 );
		}
	}

	public function register_badgeos_shortcodes() {
		badgeos_register_shortcode( array(
			'name'            => __( 'User earned achievement', 'achievement-shortcode-for-badgeos' ),
			'slug'            => 'user_earned_achievement',
			'description'     => __( 'Show or hide content depending on the user having earned a specific achievement.', 'achievement-shortcode-for-badgeos' ),
			'output_callback' => array( $this, 'shortcode' ),
			'attributes'      => array(
				'id' => array(
					'name'        => __( 'Achievement ID', 'achievement-shortcode-for-badgeos' ),
					'description' => __( 'The ID of the achievement the user must have earned.', 'achievement-shortcode-for-badgeos' ),
					'type'        => 'text',
				),
				'before' => array(
					'name'        => __( 'Date', 'achievement-shortcode-for-badgeos' ),
					'description' => __( 'Date before the achievement must have been earned in the form Ymd (optional).', 'achievement-shortcode-for-badgeos' ),
					'type'        => 'text',
				),
				'not' => array(
					'name'        => __( 'Achievement not earned', 'achievement-shortcode-for-badgeos' ),
					'description' => __( 'Specify to show content if the user has NOT yet earned the achievement.', 'achievement-shortcode-for-badgeos' ),
					'type'        => 'select',
					'values'      => array(
						'true'  => __( 'True', 'achievement-shortcode-for-badgeos' ),
						'false' => __( 'False', 'achievement-shortcode-for-badgeos' )
						),
					'default'     => 'false',
				),
			),
		) );
	}

	/**
	 * Enqueue and localize relevant admin_scripts.
	 *
	 * @since  1.0.4
	 */
	public function admin_scripts() {
		wp_enqueue_script( 'rangyinputs-jquery', $this->directory_url . 'js/rangyinputs-jquery-src.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'badgeos-achievement-shortcode-embed', $this->directory_url . 'js/achievement-shortcode-embed.js', array( 'rangyinputs-jquery', 'badgeos-select2' ), '', true );
	}

	public function shortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'id' 	 => false,    // achievement
			'before' => false,
			'not'	 => false,
		), $atts );

		$achievement = $atts['id'];
		$before		 = $atts['before'];
		$not 		 = $atts['not'] === 'true' || $atts['not'] === '1';

		$user_id = get_current_user_id();

		$user_has_achievement = false;

		if ( $user_id && $achievement ) {
			$earned_achievements = badgeos_get_user_achievements(
				array( 'user_id' => absint( $user_id ),
					   'achievement_id' =>  absint( intval( $achievement ) ) ) );

			if ( $before && is_array( $earned_achievements ) && ! empty( $earned_achievements ) ) {
				$before = DateTime::createFromFormat( 'YmdHis', "{$before}235959" );

				foreach ( $earned_achievements as $key => $achievement ) {
					// Drop any achievements after our before timestamp
					if ( $before->getTimestamp() < $achievement->date_earned )
						unset( $earned_achievements[$key] );
				}
			}

			$user_has_achievement = ! empty( $earned_achievements );
			$user_has_achievement = apply_filters( 'badgeos_has_user_earned_achievement', $user_has_achievement, $achievement, $user_id );
		}

		$return = '';

		if ( ! $achievement ) {
			$return = '<div class="error">' . __( 'You have to specify a valid achievement id in the "id" parameter!', 'achievement-shortcode-for-badgeos' ) . '</div>';
		} elseif ( ( ! $not && $user_has_achievement ) || ( $not && ! $user_has_achievement ) ) {
			$return = do_shortcode( $content );
		}

		return $return;
	}

	/**
	 * Check if BadgeOS is available
	 *
	 * @since  1.0.0
	 * @return bool True if BadgeOS is available, false otherwise
	 */
	public function meets_requirements() {

		if ( class_exists( 'BadgeOS' ) && version_compare( BadgeOS::$version, '1.4.0', '>=' ) ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Generate a custom error message and deactivates the plugin if we don't meet requirements
	 *
	 * @since 1.0.0
	 */
	public function maybe_disable_plugin() {
		if ( ! $this->meets_requirements() ) {
			// Display our error
			echo '<div id="message" class="error">';
			echo '<p>' . sprintf( __( 'BadgeOS Achievement Shortcode Add-On requires BadgeOS 1.4.0 or greater and has been <a href="%s">deactivated</a>. Please install and activate BadgeOS and then reactivate this plugin.', 'achievement-shortcode-for-badgeos' ), admin_url( 'plugins.php' ) ) . '</p>';
			echo '</div>';

			// Deactivate our plugin
			deactivate_plugins( $this->basename );
		}
	}

}

new BadgeOS_Achievement_Shortcode();
