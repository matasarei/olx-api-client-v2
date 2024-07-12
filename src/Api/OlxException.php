<?php

namespace Gentor\Olx\Api;

use Exception;

class OlxException extends Exception
{
    /**
     * @var \stdClass $details
     */
    protected $details;

    /**
     * @param \stdClass|null $details
     * @param Exception|null $previous
     */
    public function __construct($message = "", $code = 0, $details = null, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->details = $details;
    }

    /**
     * @return \stdClass
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @return string
     */
    public function getDetailsJson()
    {
        return json_encode($this->details, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function hasMissingParams(): bool
    {
        if (isset($this->details->error->details)) {
            $details = (array)$this->details->error->details;
            foreach ($details as $key => $detail) {
                if (false !== strpos($key, 'params')) {
                    return true;
                }
            }
        }

        return false;
    }
}
