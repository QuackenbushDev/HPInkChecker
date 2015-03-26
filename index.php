<?php

/*
 *
 * Developed by Christopher Quackenbush <christopher@quackenbush.me>
 * License: See license.md
 *
 */

include "simple_html_dom.php";

/**
 * Class HPInkChecker
 *
 * Collects the printers available ink for easy monitoring of
 * colour and black/white printers produced by HP.
 */
class HPInkChecker {
    public $printers = array();
    public $debug = false;

    public function enableDebug() {
        $this->debug = true;

        return $this;
    }

    public function addPrinter($name, $url, $black_only) {
        $this->printers[] = [
            'name'       => $name,
            'url'        => $url,
            'black_only' => $black_only,
            'cyan'       => 0,
            'magenta'    => 0,
            'yellow'     => 0,
            'black'      => 0
        ];

        return $this;
    }

    public function scrape() {
        foreach ($this->printers as &$printer) {
            $url = (!$this->debug) ? $printer['url'] : "./printer.html";
            $content = file_get_contents($url);
            $content = str_get_html($content);

            $printer['cyan']    = (int) $content->find('.cyan', 0)->children(0)->width;
            $printer['yellow']  = (int) $content->find('.yellow', 0)->children(0)->width;
            $printer['magenta'] = (int) $content->find('.magenta', 0)->children(0)->width;
            $printer['black']   = (int) $content->find('.black', 0)->children(0)->width;
        }

        return $this;
    }
}

$tonerCheck = new HPInkChecker();
$tonerCheck
    //->enableDebug() // Uncomment this to load a local printer html dump instead of the web ui. Name the file printer.html
    ->addPrinter('Printer A', 'http://10.10.50.50/web/guest/en/webprinter/supply.cgi', false)
    ->addPrinter('Printer B', 'http://10.10.50.51/web/guest/en/webprinter/supply.cgi', false)
    ->addPrinter('Printer C', 'http://10.10.50.52/web/guest/en/webprinter/supply.cgi', false)
    ->addPrinter('Printer D', 'http://10.10.50.53/web/guest/en/webprinter/supply.cgi', false)
    ->scrape();

?>

<html>
    <head>
        <style type="text/css">
            .printer {}
        </style>
    </head>
    <body>
        <div class="printer">
            <?php
                foreach ($tonerCheck->printers as $printer) {
                    echo "<div class='printer'>";
                      echo "<p>Name:   " . $printer['name']    . "</p>";
                      echo "<p>URL:    " . $printer['url']     . "</p>";
                      echo "<p>" . $printer['cyan']    . "</p>";
                      echo "<p>" . $printer['magenta'] . "</p>";
                      echo "<p>" . $printer['yellow']  . "</p>";
                      echo "<p>" . $printer['black']   . "</p>";
                    echo "</div>";
                }
            ?>
        </div>
    </body>
</html>
