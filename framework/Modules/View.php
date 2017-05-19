<?php

class View
{
    /**
     * @param $template
     * @param $values
     * @param bool $return
     * @return string
     */

    public static function render($template,$values,$return = false)
    {
        if (file_exists($template)) {
            $fullPath = explode(DIRECTORY_SEPARATOR,$template);
            $path = '';
            for ($i = 0; $i < count($fullPath) - 1; $i++) {
                $path .= $fullPath[$i] . DIRECTORY_SEPARATOR;
            }
            $loader = new Twig_Loader_Filesystem($path);
            $twig = new Twig_Environment($loader);

            $content = $twig->render(end($fullPath),$values);

            if ($return) {
                return $content;
            } else {
                echo $content;
            }

        } else {
            echo "Wrong path {$template}";
        }
    }
}