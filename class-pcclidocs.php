<?php
/**
 * Command class
 *
 * @package pc-cli-docs
 */

/**
 * CLI class.
 */
class PCCLIDocs {

	/**
	 * Target directory.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $dir = 'docs/';

	/**
	 * Target file name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $file = 'CLI.md';

	/**
	 * Generate docs.
	 *
	 * @since 1.0.0
	 */
	public function __invoke() {
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
			file_put_contents( WP_PLUGIN_CHECK_PLUGIN_DIR_PATH . $this->dir . $this->file, trim( $content ) );
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
