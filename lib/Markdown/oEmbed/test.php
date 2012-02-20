<?php

include 'oembed.php';

$oembed = new oEmbed();
$oembed->addProvider(new oEmbedProvider(
	'http://www.flickr.com/services/oembed/',
	'http://www.flickr.com/photos/*'
));

$oembed->addProvider(new oEmbedProvider(
	'http://www.youtube.com/oembed',
	Array (
		'http://youtu.be/watch*',
		'http://*.youtube.com/watch*',
		'http://youtube.com/watch*'
	)
));

$oembed->addProvider(new oEmbedProviderImage(null, null));

//var_dump($oembed->getMetaDataForUrl("http://www.flickr.com/photos/mlebkowski/1285312626/in/photostream"));
//var_dump($oembed->getMetaDataForUrl("http://puck.one.pl/x46k4.png", Array ('maxwidth' => 800)));

var_dump($oembed->getMetaDataForUrl("http://www.youtube.com/watch?v=Z5Cz9MkLNxw"));
