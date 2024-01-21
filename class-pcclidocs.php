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
		$commands = array( 'check', 'list-checks', 'list-check-categories' );

		$content = '';

		foreach ( $commands as $command ) {
			$method = str_replace( '-', '_', $command );

			$reflection = new ReflectionMethod( 'WordPress\Plugin_Check\CLI\Plugin_Check_Command', $method );

			$content .= "# wp plugin {$command} \n";
			$content .= "\n";

			$parser = new WP_CLI\DocParser( $reflection->getDocComment() );

			$short_description = $parser->get_shortdesc();

			$content .= "{$short_description} \n";

			list ( $options, $examples ) = explode( '## EXAMPLES', $parser->get_longdesc() );

			$content .= "## OPTIONS \n";
			$content .= $this->get_wrapped( $options );

			$content .= "## EXAMPLES \n";
			$content .= $this->get_wrapped( $examples );

			$content .= "\n";
		}

		if ( ! empty( $content ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents( WP_PLUGIN_CHECK_PLUGIN_DIR_PATH . $this->dir . $this->file, $content );
			WP_CLI::success( 'Docs generated successfully.' );
			return;
		}

		WP_CLI::error( 'Error generating docs.' );
	}

	/**
	 * Return content wrapped with Markdown code.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The content.
	 * @return string Wrapped content.
	 */
	private function get_wrapped( $content ) {
		return "\n```" . $content . "\n```\n";
	}
}
