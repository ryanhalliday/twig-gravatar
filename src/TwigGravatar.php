<?php

class TwigGravatar extends \Twig_Extension {
	public $baseUrl = "http://www.gravatar.com/";
	public $httpsUrl = "https://secure.gravatar.com/";

	private $default;
	private $size;
	private $filterPrefix;
	private $rating;
	private $useHttps;

	private $filterOptions = array("is_safe" => array("html"));

	private $defaults = array(
		"404", "mm", "identicon", "monsterid", "wavatar", "retro", "blank"
	);
	private $ratings = array(
		"g", "pg", "r", "x"
	);

	public function __construct($default = null, $size = null, $filterPrefix = 'gr', $rating = null, $useHttps = true)
	{
		$this->default = $default;
		$this->size = $size;
		$this->filterPrefix = $filterPrefix;
		$this->rating = $rating;
		$this->useHttps = $useHttps;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilters() {
		if ($this->filterPrefix !== 'gr') {
			return array(
				new \Twig_SimpleFilter($this->filterPrefix . 'Avatar', array($this, 'avatar'), $this->filterOptions),
				new \Twig_SimpleFilter($this->filterPrefix . 'Https', array($this, 'https'), $this->filterOptions),
				new \Twig_SimpleFilter($this->filterPrefix . 'Size', array($this, 'size'), $this->filterOptions),
				new \Twig_SimpleFilter($this->filterPrefix . 'Default', array($this, 'def'), $this->filterOptions),
				new \Twig_SimpleFilter($this->filterPrefix . 'Rating', array($this, 'rating'), $this->filterOptions)
			);
		}

		return array(
			new \Twig_SimpleFilter('grAvatar', array($this, 'avatar'), $this->filterOptions),
			new \Twig_SimpleFilter('grHttps', array($this, 'https'), $this->filterOptions),
			new \Twig_SimpleFilter('grSize', array($this, 'size'), $this->filterOptions),
			new \Twig_SimpleFilter('grDefault', array($this, 'def'), $this->filterOptions),
			new \Twig_SimpleFilter('grRating', array($this, 'rating'), $this->filterOptions)
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
			$baseUrl = $this->useHttps ? $this->https($this->baseUrl) : $this->baseUrl;
			$url = $baseUrl . "avatar/" . $this->generateHash($email);
		} else {
			throw new InvalidArgumentException("The avatar filter must be passed a valid Email address");
		}

		if ($this->rating) {
			$url = $this->rating($url, $this->rating);
		}

		if ($this->size) {
			$url = $this->size($url, $this->size);
		}

		if ($this->default) {
			$url = $this->def($url, $this->default);
		}

		return $url;
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
	public function size($value, $px) {
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
	 * @param string  $default
	 * @param boolean $force   Always load the default image
	 * @return string          Gravatar URL with a default image.
	 */
	public function def($value, $default, $force = false) {
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
                        $addition = array("default" => $default);
                        if ($force) {
                            $addition["forcedefault"] = 'y';
                        }
			return $this->query($value, $addition);
		}
	}

	/**
	 * Specify the maximum rating for an avatar
	 * @param  string $value
	 * @param  string $rating Expects g,pg,r or x
	 * @return string Gravatar URL with a rating specified
	 */
	public function rating($value, $rating) {
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
		parse_str(parse_url( $string, PHP_URL_QUERY), $queryList);

		foreach ($addition as $key => $value) {
			$queryList[$key] = $value;
		}

		$url = sprintf(
			'%s://%s%s%s',
			parse_url($string, PHP_URL_SCHEME),
			parse_url($string, PHP_URL_HOST),
			parse_url($string, PHP_URL_PATH),
			!empty($queryList) ? '?'.http_build_query($queryList) : ''
		);

		return $url;
	}

	/**
	 * Get the Extension name
	 * @return string
	 */
	public function getName() {
		return 'TwigGravatar';
	}
}
