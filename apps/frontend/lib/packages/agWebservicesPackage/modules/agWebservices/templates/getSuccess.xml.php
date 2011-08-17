<?
$xml = '<?xml version="1.0" encoding="utf-8" ?>';
$xml .= '<results>';
foreach ($results as $k => $entity) {
    $xml .= '<entity type="'.$type.'">';
    foreach ($entity as $key => $value) {
        $xml .= '<'.$key.'>'.dump($value).'</'.$key.'>';
    }
    $xml .= '</entity>';
}
$xml .= '</results>';

function dump($value) 
{
    if (is_null($value)) {
        return ;
    }

    $response = '';
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            $response .= "<{$k}>".dump($v)."</{$k}>";
        }
    } else {
        if (is_object($value)) {
            $response .= dump($value->getRawValue());
        } else {
            $response = $value;
        }
    }
    return $response;
}

/**
 * Receives a flat XML string and make it well-formatted
 * @author http://recursive-design.com/blog/2007/04/05/format-xml-with-php/
 * @param string $xml The original XML string to process
 * @return string Well-formatted version of the original XML string
 */
function formatXmlString($xml) 
{
    // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
    $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

    // now indent the tags
    $token      = strtok($xml, "\n");
    $result     = ''; // holds formatted version as it is built
    $pad        = 0; // initial indent
    $matches    = array(); // returns from preg_matches()

    // scan each line and adjust indent based on opening/closing tags
    while ($token !== false) {
        // test for the various tag states

        // 1. open and closing tags on same line - no change
        if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) {
            $indent=0;
            // 2. closing tag - outdent now
        } elseif (preg_match('/^<\/\w/', $token, $matches)) {
            $pad--;
            $indent=0;
            // 3. opening tag - don't pad this one, only subsequent tags
        } elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) {
            $indent=1;
            // 4. no indentation needed
        } else {
            $indent=0;
        }

        // pad the line with the required number of leading spaces
        $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
        $result .= $line . "\n"; // add to the cumulative result, with linefeed
        $token   = strtok("\n"); // get the next token
        $pad    += $indent; // update the pad size for subsequent lines
    }

    return $result;
}

echo formatXmlString($xml);

</results>