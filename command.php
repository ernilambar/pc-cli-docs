<?php
/**
 * Command
 *
 * @package pc-cli-docs
 */

$command = new PCP_Command();

WP_CLI::add_command( 'pcp', $command );
