<?php
use Disqus\Export\Processor as ExportProcessor,
    Disqus\Export\Exporter\EzComments as EzCommentsExporter,
    Disqus\Export\Formatter\DisqusWXR as DisqusFormatter;

$processor = new ExportProcessor(
    new EzCommentsExporter(),
    new DisqusFormatter()
);
$processor->export();
echo $processor->render();
