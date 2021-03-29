
<?php
class SMMLayer

	{
	public

	function GetInstagramAvatar($InstagramID)
		{
		$url = 'http://instagram.com/' . $InstagramID;
		$options = array(
			'http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'POST',
				'content' => 'http://instagram.com/'
			)
		);
		$context = stream_context_create($options);
		@$result = file_get_contents($url, false, $context);
		$doc = new DomDocument();
		@$doc->loadHTML($result);
		$xpath = new DOMXPath($doc);
		$query = '//*/meta';
		$metas = $xpath->query($query);
		$rmetas = array();
		foreach($metas as $meta)
			{
			$property = $meta->getAttribute('property');
			$content = $meta->getAttribute('content');
			if (!empty($property) && preg_match('#^og:#', $property))
				{
				$rmetas[$property] = $content;
				}
			}

		if (!empty($rmetas) && $result == true) return $rmetas['og:image'];
		  else return 'https://conferencecloud-assets.s3.amazonaws.com/default_avatar.png';
		}
	}

$SMMLayer = new SMMLayer();
