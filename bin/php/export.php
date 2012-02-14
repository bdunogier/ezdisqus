<?php
/**
 * Main export script
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */
use Disqus\Export\Processor as ExportProcessor;

require 'autoload.php';

$cli = eZCLI::instance();
$cli->setUseStyles(true);
$script = eZScript::instance( array( 'description' => ( "Comment export script to be re-imported into the Disqus service\n"),
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true ) );

$script->startup();

// Options processing
$options = $script->getOptions(
    '[exporter:][formatter:][options:][split]',
    '',
    array(
        'exporter'                  => 'Exporter to use, as defined in disqus.ini',
        'formatter'                 => 'Formatter to use. Default is DisqusWXR formatter',
        'exporter-options'          => 'Options for exporter. Should be something like --exporter-options="foo=bar,foo2=baz"',
        'formatter-options'         => 'Options for formatter. Should be something like --formatter-options="foo=bar,foo2=baz"',
        'split'                     => 'Split the export into several files if needed'
    )
);
$script->initialize();
$script->setUseDebugAccumulators( true );

try
{
    $disqusINI = eZINI::instance( 'disqus.ini' );
    $availableExporters = $disqusINI->variable( 'ExportSettings', 'Exporters' );
    $availableFormatters = $disqusINI->variable( 'ExportSettings', 'Formatters' );

    // Check exporter validity
    if ( !isset( $options['exporter'] ) )
        throw new Exception( 'You must provide an exporter!' );
    if ( !isset( $availableExporters[$options['exporter']] ) )
        throw new Exception( "Invalid exporter '{$options['exporter']}'. Valid exporters are: " . implode( ', ', array_keys(  $availableExporters ) ) );
    $exporter = new $availableExporters[$options['exporter']];

    // Check formatter validity
    if ( !isset( $options['formatter'] ) )
        $options['formatter'] = 'disquswxr';
    else if ( !isset( $availableFormatters[$options['formatter']] ) )
        throw new Exception( "Invalid formatter '{$options['formatter']}'. Valid formatters are: " . implode( ', ', array_keys( $availableFormatters ) ) );
    $formatter = new $availableFormatters[$options['formatter']];

    // Now export
    $cli->notice( "Now exporting with {$exporter->getName()} exporter and {$formatter->getName()} formatter..." );
    $processor = new ExportProcessor( $exporter, $formatter );
    $processor->export();

    // Rendering or splitting ?
    $exportDir = eZSys::varDirectory();
    $exportBaseFilename = 'comments-export';
    $exportFormat = $processor->getExportFormat();
    if ( isset( $options['split'] ) )
    {
        $cli->notice( 'Now splitting final file.' );
        $splittedData = $processor->split();
        $cli->notice( 'Total size before splitting is ' . number_format( $splittedData->totalSize / 1024 / 1024, 2 ) . 'M' );
        foreach ( $splittedData->stringArray as $i => $data )
        {
            $filename = "$exportBaseFilename.$i.$exportFormat";
            $cli->notice( "Now rendering $exportDir/$filename" );
            eZFile::create( "$filename", $exportDir, $data, true );
        }
    }
    else
    {
        $cli->notice( "Now rendering to $exportDir/$exportBaseFilename.$exportFormat" );
        eZFile::create( "$exportBaseFilename.$exportFormat", $exportDir, $processor->render(), true );
    }

    $memoryMax = memory_get_peak_usage();
    $memoryMax = round( $memoryMax / 1024 / 1024, 2 ); // Convert in Megabytes
    $cli->notice( 'Peak memory usage : '.$memoryMax . 'M' );

    $script->shutdown();
}
catch( Exception $e )
{
    $errCode = $e->getCode();
    $errCode = $errCode != 0 ? $errCode : 1; // If an error has occured, script must terminate with a status other than 0
    $cli->error( $e->getMessage() );
    $script->shutdown( $errCode );
}
