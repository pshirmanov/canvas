<?php
/**
 * Designed for the return of content
 */
class Response {

    /**
     * Status page
     * @var integer
     */
	private $status;

    /**
     * Sait content
     * @var string
     */
	private $content;

    /**
     * Response constructor
     * @param integer $status  status page
     * @param string $content sait content
     */
	function __construct($status, $content) {
		$this->status = $status;
		$this->content = $content;
	}

    /**
     * Return content page
     * @return void
     */
	public function returnPage() {
		echo $this->content;
	}
}