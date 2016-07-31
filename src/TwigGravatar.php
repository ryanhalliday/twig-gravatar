<?php

class TwigGravatar extends \Twig_Extension {
	public $baseUrl = "http://www.gravatar.com/";
	public $httpsUrl = "https://secure.gravatar.com/";

	public $filterPrefix = "gr";

	private $filterOptions = array("is_safe" => array("html"));

	private $defaults = array(
		"404", "mm", "identicon", "monsterid", "wavatar", "retro", "blank"
	);
	private $ratings = array(
		"g", "pg", "r", "x"
	);

	/**
	 * {@inheritdoc}
	 */
	public function getFilters(){
        return array(
            new \Twig_SimpleFilter($this->filterPrefix . 'Avatar', array($this, 'avatar'), $this->filterOptions),
            new \Twig_SimpleFilter($this->filterPrefix . 'Https', array($this, 'https'), $this->filterOptions),
            new \Twig_SimpleFilter($this->filterPrefix . 'Size', array($this, 'size'), $this->filterOptions),
            new \Twig_SimpleFilter($this->filterPrefix . 'Default', array($this, 'def'), $this->filterOptions),
            new \Twig_SimpleFilter($this->filterPrefix . 'Rating', array($this, 'rating'), $this->filterOptions)
        );
    }

	/**
	 * Get a Gravatar Avatar URL
	 * @param  string $email Gravatar Email address
	 * @return string        Gravatar Avatar URL
	 * @throws \InvalidArgumentException If $email is invalid
	 */
	public function avatar($email) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return $this->baseUrl . "avatar/" . $this->generateHash($email);
		} else {
			throw new InvalidArgumentException("The avatar filter must be passed a valid Email address");
		}
	}

	/**
	 * Change a Gravatar URL to its Secure version
	 * @param  string $value URL to convert
	 * @return string        Converted URL
	 * @throws \InvalidArgumentException If $value isn't an existing Gravatar URL
	 */
	public function https($value) {
		if (strpos($value, $this->baseUrl) === false) {
			throw new InvalidArgumentException("You can only convert existing Gravatar URLs to HTTPS");
		}
		else {
			return str_replace($this->baseUrl, $this->httpsUrl, $value);
		}
	}

	/**
	 * Change the Size of a Gravatar URL
	 * @param  string  $value
	 * @param  integer $px
	 * @return string Sized Gravatar URL
	 */
	public function size($value, $px = 100) {
		if (!is_numeric($px) || $px < 0 || $px > 2048) {
			throw new InvalidArgumentException("You must pass the size filter a valid number between 0 and 2048");
		}
		else if (strpos($value, $this->baseUrl) === false
			&& strpos($value, $this->httpsUrl) === false) {
			throw new InvalidArgumentException("You must pass the size filter an existing Gravatar URL");
		}
		else {
			return $this->query($value, array("size" => $px));
		}
	}

	/**
	 * Specify a default Image for when there is no matching Gravatar image.
	 * @param string  $value
	 * @param string  $default Defaults to Mystery Man
	 * @param boolean $force   Always load the default image
	 * @return string          Gravatar URL with a default image.
	 */
	public function def($value, $default = "mm", $force = false) {
		if (strpos($value, $this->baseUrl) === false && strpos($value, $this->httpsUrl) === false) {
			throw new InvalidArgumentException("You can only a default to existing Gravatar URLs");
		}
		else if (!filter_var($default, FILTER_VALIDATE_URL) && !in_array($default, $this->defaults)) {
			throw new InvalidArgumentException("Default must be a URL or valid default");
		}
		else if (!is_bool($force)) {
			throw new InvalidArgumentException("The force option for a default must be boolean");
		}
		else {
			if (filter_var($default, FILTER_VALIDATE_URL)) $default = urlencode($default);
			$force = ($force ? "y" : "n");
			return $this->query($value, array("default" => $default, "forcedefault" => $force));
		}
	}

	/**
	 * Specify the maximum rating for an avatar
	 * @param  string $value
	 * @param  string $rating Expects g,pg,r or x
	 * @return string Gravatar URL with a rating specified
	 */
	public function rating($value, $rating = "g") {
		if (strpos($value, $this->baseUrl) === false && strpos($value, $this->httpsUrl) === false) {
			throw new InvalidArgumentException("You can only add a rating to an existing Gravatar URL");
		}
		else if (!in_array(strtolower($rating), $this->ratings)) {
			throw new InvalidArgumentException("Rating must be g,pg,r or x");
		}
		else {
			return $this->query($value, array("rating" => $rating));
		}
	}


	/**
	 * Generate the Hashed email address
	 * @param  string $email
	 * @return string        Hashed email address
	 */
	public function generateHash($email) {
		return md5(strtolower(trim($email)));
	}

	/**
	 * Generate the query string
	 * @param  string $string
	 * @param  array  $addition Array of what parameters to add
	 * @return string
	 */
	private function query($string, array $addition) {
		foreach ($addition as $name => $value) {
			$string .= (strpos($string, "?") === FALSE ? "?" : "&") . $name . "=" . $value;
		}
		return $string;
	}

	/**
	 * Get the Extension name
	 * @return string
	 */
	public function getName() {
		return 'TwigGravatar';
	}
}