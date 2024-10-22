<?php
/**
 * Command class
 *
 * @package pc-cli-docs
 */

/**
 * CLI class.
 */
class PCP_Command {

	/**
	 * Target directory.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $dir = 'docs/';

	/**
	 * Generate checks docs.
	 *
	 * @since 1.0.0
	 *
	 * @subcommand checks-docs
	 */
	public function checks_docs() {
		// Bail if Plugin Check is not active.
		if ( ! defined( 'WP_PLUGIN_CHECK_PLUGIN_DIR_PATH' ) ) {
			WP_CLI::error( 'Plugin Check is not active.' );
			return;
		}

		$content = '';

		$content .= "[Back to overview](./README.md)\n\n";

		$content .= "# Available Checks\n\n";

		$result = WP_CLI::runcommand(
			'plugin list-checks --format=json',
			array(
				'return'     => true,
				'parse'      => 'json',
				'launch'     => true,
				'exit_error' => false,
			)
		);

		// Error occurred.
		if ( empty( $result ) || ! is_array( $result ) ) {
			WP_CLI::line( $result );
			WP_CLI::halt( 1 );
		}

		$content .= '| Check | Category | Description | Documentation |';
		$content .= "\n";
		$content .= '| --- | --- | --- | --- |';

		foreach ( $result as $check ) {
			$content .= "\n";
			$content .= '| ' . $check['slug'] . ' | ' . $check['category'] . ' | ' . $check['description'] . ' | ';

			$link = ' ';

			if ( ! empty( $check['url'] ) ) {
				$link = '[Learn more](' . $check['url'] . ')';
			}

			$content .= $link . ' |';
		}

		if ( ! empty( $content ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents( WP_PLUGIN_CHECK_PLUGIN_DIR_PATH . $this->dir . 'checks.md', trim( $content ) );
			WP_CLI::success( 'Checks file generated successfully.' );
			return;
		}

		WP_CLI::error( 'Error generating checks file.' );
	}

	/**
	 * Generate CLI docs.
	 *
	 * @since 1.0.0
	 *
	 * @subcommand cli-docs
	 */
	public function cli_docs() {
		// Bail if Plugin Check is not active.
		if ( ! defined( 'WP_PLUGIN_CHECK_PLUGIN_DIR_PATH' ) ) {
			WP_CLI::error( 'Plugin Check is not active.' );
			return;
		}

		$commands = array( 'check', 'list-checks', 'list-check-categories' );

		$content = '';

		$content .= "[Back to overview](./README.md)\n\n";

		foreach ( $commands as $command ) {
			$method = str_replace( '-', '_', $command );

			$reflection = new ReflectionMethod( 'WordPress\Plugin_Check\CLI\Plugin_Check_Command', $method );

			$content .= "# wp plugin {$command}\n";
			$content .= "\n";

			$parser = new WP_CLI\DocParser( $reflection->getDocComment() );

			$short_description = $parser->get_shortdesc();

			$content .= "{$short_description}\n\n";

			$options  = '';
			$examples = '';

			$exploded = explode( '## EXAMPLES', $parser->get_longdesc() );

			if ( 1 === count( $exploded ) ) {
				$options = reset( $exploded );
			} elseif ( 2 === count( $exploded ) ) {
				$options  = $exploded[0];
				$examples = $exploded[1];
			}

			if ( ! empty( $options ) ) {
				$content .= '## OPTIONS';
				$options  = str_replace( '## OPTIONS', '', $options );
				$content .= $this->get_wrapped( trim( $options ) );
			}

			if ( ! empty( $examples ) ) {
				$content .= '## EXAMPLES';
				$content .= $this->get_wrapped( $this->get_clean_examples( $examples ) );
			}

			$content .= "\n";
		}

		if ( ! empty( $content ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents( WP_PLUGIN_CHECK_PLUGIN_DIR_PATH . $this->dir . 'CLI.md', trim( $content ) );
			WP_CLI::success( 'Docs generated successfully.' );
			return;
		}

		WP_CLI::error( 'Error generating docs.' );
	}

	/**
	 * Returns content wrapped with Markdown code.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The content.
	 * @return string Wrapped content.
	 */
	private function get_wrapped( $content ) {
		return "\n```\n" . $content . "\n```\n";
	}

	/**
	 * Returns cleaned examples.
	 *
	 * @since 1.0.0
	 *
	 * @param string $examples The content.
	 * @return string Cleaned up examples.
	 */
	private function get_clean_examples( $examples ) {
		$temp_examples = explode( "\n", $examples );
		$temp_examples = array_filter( $temp_examples );
		$temp_examples = array_map( 'trim', $temp_examples );
		return implode( "\n", $temp_examples );
	}
}
