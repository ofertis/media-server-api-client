<?php

namespace Ofertis\MediaServer;

use CURLFile;

class MediaServer
{
    protected $config;

    protected $prefix;

    protected $folder;

    /**
     * MediaServer service constructor.
     * ------------------------
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        if($config)
            $this->config = $config;
        else
            $this->config = config('media-server');
    }

    /**
     * @param CURLFile $file
     * @param array $params
     * @return array
     */
    public function upload(CURLFile $file, array $params = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->config['uri'] . '/upload');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept" => "application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge([ 'media-upload' => $file ], $params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch); curl_close ($ch);

        $result = json_decode($result, true);

        return isset($result[0]) ? $result[0] : [];
    }

    /**
     * @param $domain
     * @param $path
     * @param array $params
     * @return string
     */
    public function getImageLink($path, $params = []) {
        // Setting UP X axe
        $axes = isset($params['axis-x']) && !empty($params['axis-x']) && is_numeric($params['axis-x']) ? $params['axis-x'] : '';
        // Setting UP Y axe
        $axes .= isset($params['axis-y']) && !empty($params['axis-y']) && is_numeric($params['axis-y']) ? 'x' . $params['axis-y'] : '';
        // Setting UP action type
        $action = isset($params['action']) && !empty($params['action']) ? $params['action'] : '';
        // Setting UP action params
        $action .= isset($params['action-params']) && !empty($params['action']) ? '|' . $params['action-params'] : '';
        // Explode path from adding params
        $file = explode('.', $path);
        // Insert AXES to media path
        if (!empty($axes)) {
            $axes = '_' . $axes;
        }
        // Insert ACTION to media path
        if (!empty($action)) {
            $action = '_' . $action;
        }
        // Create OUTPUT
        return $this->getLink(sprintf($file[0] . '%s%s.' . $file[1], $axes, $action));
    }

    /**
     * @param $string
     * @param null $mime
     * @param null $name
     * @return CURLFile
     */
    public function getCURLFileFromString($string, $mime = null, $name = null)
    {
        $path = tempnam("/tmp", "CURL_FILE_STRING");
        file_put_contents($path, $string);

        return new \CURLFile($path, isset($mime) ? $mime : mime_content_type($path), $name);
    }

    /**
     * @param $path
     * @param null $mime
     * @param null $name
     * @return CURLFile
     */
    public function getCURLFile($path, $mime = null, $name = null)
    {
        return new \CURLFile($path, isset($mime) ? $mime : mime_content_type($path), $name);
    }

    /**
     * @param string|array $mediaPath
     *
     * @return array|bool|string
     */
    public function getContents($mediaPath)
    {
        if(is_array($mediaPath))
        {
            $items = [];
            foreach($mediaPath AS $key => $item)
            {
                if($contents = file_get_contents($this->getLink($item)))
                {
                    $items[$key] = $contents;
                };
            }

            return $items;
        }
        else
        {
            return file_get_contents($this->getLink($mediaPath));
        }
    }

    public function getLink($mediaPath){
        return $this->config['uri'] . DIRECTORY_SEPARATOR . $mediaPath;
    }
}