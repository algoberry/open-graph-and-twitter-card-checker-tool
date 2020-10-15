<?php
//--
$outerHead = "<head>";
$outerHeadLength = strlen($outerHead);
$outerHeadStart = 0;

$innerHead = "</head>";
$innerHeadLength = strlen($innerHead);
$innerHeadStart = 0;
//--

//--
$outerMeta = "<meta";
$innerMeta = ">";
$metaPointer = 0;
//--

//--
$metaPropertyBase = "property=''";
$metaPropertyPointer = 0;
//--

//--
$metaContentBase = "content=''";
$metaContentPointer = 0;
//--

$crawlOptions = array(
CURLOPT_RETURNTRANSFER => true,     		// return web page
CURLOPT_HEADER         => false,    		// don't return headers
CURLOPT_FOLLOWLOCATION => true,     		// follow redirects
CURLOPT_ENCODING       => "",       		// handle all encodings
CURLOPT_USERAGENT      => "algoberrybot", 	// who am i
CURLOPT_AUTOREFERER    => true,     		// set referer on redirect
CURLOPT_CONNECTTIMEOUT => 10,      			// timeout on connect
CURLOPT_TIMEOUT        => 10,      			// timeout on response
CURLOPT_MAXREDIRS      => 0       			// stop after 10 redirects
);

$metaName = "";
$metaContent = "";
$openGraphList = array();
$twitterCardList = array();
$noMatchList = array();


$openGraphMetadata = array("og:title","og:type","og:image","og:url","og:audio","og:description","og:determiner","og:locale","og:locale:alternate","og:site_name","og:video","og:image","og:image:secure_url","og:image:type","og:image:width","og:image:height","og:image:alt","og:video","og:video:secure_url","og:video:type","og:video:width","og:video:height","og:audio","og:audio:secure_url","og:audio:type","music.song","music:duration","music:album","music:album:disc","music:album:track","music:musician","music:song:disc","music:song:track","music:musician","music:release_date","music.playlist","music.radio_station","music:creator","video.movie","video:actor","video:actor:role","video:director","video:writer","video:duration","video:release_date","video:tag","video.episode","video:series","video.tv_show","video.other","article:published_time","article:modified_time","article:expiration_time","article:author","article:section","article:tag","book:author","book:isbn","book:release_date","book:tag","profile:first_name","profile:last_name","profile:username","profile:gender");
$twitterCardMetadata = array("twitter:card","twitter:site","twitter:creator","twitter:url","twitter:title");


function checkCategory($type) {
	global $openGraphMetadata,$twitterCardMetadata;
	$category = "opengraph";
	foreach($openGraphMetadata as $key => $value) {
		if($type == $value) {
			return $category;
		}
	}
	$category = "twittercard";
	foreach($twitterCardMetadata as $key => $value) {
		if($type == $value) {
			return $category;
		}
	}
	$category = "nomatch";
	return $category;
}

function showList($base,$category) {
	?>
	<div style="margin-bottom:40px;">
	<h3><?php echo $category; ?></h3>
	<table width="100%">
	<tr>
		<th valign='top'>SR.No</th>
		<th valign='top'>Tag Name</th>
		<th valign='top'>Value</th>
	</tr>
	<?php
	$count = 1;
	foreach($base as $key => $value) {
		if(count($value)>=2) {
			foreach($value as $innerKey => $innerValue) {
				echo "<tr><td>".$count."</td><td>".$key."</td><td>".$innerValue."</td></tr>";
				$count++;
			}
		}
		else
		{
			echo "<tr><td>".$count."</td><td>".$key."</td><td>".$value[0]."</td></tr>";
			$count++;
		}
	}
	?>
	</table>
	</div>
	<?php
}

$url = "";
$validURL = "";
if(isset($_POST["auditbutton"]) && isset($_POST["url"])) {
	$url = trim($_POST["url"]);
	if($url != "") {
		if(filter_var($url,FILTER_VALIDATE_URL) == true) {
			$validURL = $url; 
		}
	}
	else
	{
		$url = " ";
	}
}
?>
<html>
	<head>
		<title>Open Graph & Twitter Card Checker Tool</title>
		<style>
		body {
		font-family: 'Merriweather', serif;
		font-size:14px;
		}

		h1 {
		font-size:16px;
		}

		table {
		border-collapse: collapse;
		width: 100%;
		}

		th {
		font-size:15px;
		}
		
		td, th {  
		border: 1px solid #ddd;
		text-align: left;
		padding: 15px;
		}
		
		.error {
		color:red;
		}	
		</style>
	</head>
	<body>
		<h2>Open Graph & Twitter Card Checker Tool</h2>
		<div>
			<form action="open-graph-checker.php" method="post">
			<input type="input" name="url" value="<?php echo $url; ?>" placeholder="https://www.algoberry.com"/>
			<input type="submit" name="auditbutton" value="Audit"/>
			</form>
		</div>
		<div style="margin-top:10px;margin-bottom:15px;">
		<?php
		if($validURL != "") {
			$curlObject = curl_init($validURL);
			curl_setopt_array($curlObject,$crawlOptions);
			$webPageContent = curl_exec($curlObject);
			$errorNumber = curl_errno($curlObject);
			curl_close($curlObject);
			if($errorNumber == 0) {
				$webPageCounter = 0;
				$webPageLength = strlen($webPageContent);
				//-------- Head Session Start  --------
				while($webPageCounter < $webPageLength) {
					$character = $webPageContent[$webPageCounter];
					//-------- Escape Character Start  --------
					if($character == "") {	
						$webPageCounter++;	
						continue;
					}
					//-------- Escape Character End  --------
					//-------- Outer Head Start  --------
					if($outerHead[$outerHeadStart] == $character) {
						$outerHeadStart++;
						if($outerHeadStart == $outerHeadLength) {
							$outerHeadStart = 0;
							$webPageCounter++;
							while($webPageCounter < $webPageLength) {
								$character = $webPageContent[$webPageCounter];
								//-------- Escape Character Start  --------
								if($character == "") {	
									$webPageCounter++;	
									continue;
								}
								//-------- Escape Character End  --------
								//-------- Outer Meta Start  --------
								if($outerMeta[$metaPointer] == $character) {
									$metaPointer++;
									if($metaPointer == 5) {
										$metaPointer = 0;
										$webPageCounter++;
										while($webPageCounter < $webPageLength) {
											$character = $webPageContent[$webPageCounter];
											//-------- Escape Character Start  --------
											if($character == "") {	
												$webPageCounter++;	
												continue;
											}
											//-------- Escape Character End  --------
											//-------- Meta Property Start  --------
											if($metaPropertyBase[$metaPropertyPointer] == $character || ($metaPropertyPointer == 9 && $character == "\"")) {
												if($metaPropertyPointer == 9) {
													$metaPropertyPointer = 0;
													$webPageCounter++;
													while($webPageCounter < $webPageLength) {
														if($webPageContent[$webPageCounter] != "'" && $webPageContent[$webPageCounter] != "\"")
														{	$metaName .= $webPageContent[$webPageCounter]; }
														else
														{	break;	}
														$webPageCounter++;
													}
													$metaName = trim($metaName);
												}
												else
												{	$metaPropertyPointer++;	}
											}
											else
											{	$metaPropertyPointer = 0;	}
											//-------- Meta Property End  --------
											//-------- Meta Content Start  --------
											if($metaContentBase[$metaContentPointer] == $character || ($metaContentPointer == 8 && $character == "\"")) {
												if($metaContentPointer == 8) {
													$metaContentPointer = 0;
													$webPageCounter++;
													$titleStart = $webPageCounter;
													$titleEnd = $webPageCounter;
													while($webPageCounter < $webPageLength) {
														if($webPageContent[$webPageCounter] != "'" && $webPageContent[$webPageCounter] != "\"")
														{	$metaContent .= $webPageContent[$webPageCounter]; }
														else
														{	
															$titleEnd = $webPageCounter - 1;
															break;	
														}
														$webPageCounter++;
													}
													$metaContent = trim($metaContent);
												}
												else
												{	$metaContentPointer++;	}
											}
											else
											{	$metaContentPointer = 0;	}
											//-------- Meta Content End  --------
											//-------- Inner Meta Start  --------
											if($character == $innerMeta) {
												if($metaName != "") {
													$metaName = strtolower($metaName);
													$type = checkCategory($metaName);
													if($type == "opengraph") {
														$openGraphList[$metaName][] = $metaContent;
													}
													else if($type == "twittercard") {
														$twitterCardList[$metaName][] = $metaContent;
													} 
													else
													{
														$noMatchList[$metaName][] = $metaContent;
													}
												}
												$metaName = "";
												$metaContent = "";
												break;
											}
											//-------- Inner Meta End  --------
											$webPageCounter++;
										}
									}
								}
								else
								{	$metaPointer = 0;	}
								//-------- Outer Meta End  --------
								//-------- Inner Head Start  --------
								if($innerHead[$innerHeadStart] == $character) {
									$innerHeadStart++;
									if($innerHeadStart == $innerHeadLength) {
										$innerHeadStart = 0;
										$headFound = 1;
										break;
									}
								}
								else if($innerHeadStart != 6)
								{	$innerHeadStart = 0;	}
								//-------- Inner Head End  --------
								$webPageCounter++;
							}
							if($headFound == 1) {	
								$webPageCounter++;
								break;	
							}
						}
					}
					else if($outerHeadStart != 5)
					{	$outerHeadStart = 0;	}
					//-------- Outer Head End  --------
					$webPageCounter++;
				}
				//-------- Head Session End  --------
				
				if(count($openGraphList)>=1) {
					showList($openGraphList,"Open Graph Tags");
				}
				if(count($twitterCardList)>=1) {
					showList($twitterCardList,"Twitter Card Tags");
				}
				if(count($noMatchList)>=1) {
					showList($noMatchList,"Miscellaneous Tags");
				}
			}
			else
			{
			?>
			<div class="error">Unable to access now</div>
			<?php
			}
		}
		else
		{
			if($url != "") {
			?>
			<div class="error">Please enter valid URL</div>
			<?php
			}	
		}
		?>
		</div>
		<div>
		Created by <a href = "https://www.algoberry.com" target="_blank">https://www.algoberry.com</a>
		</div>
	</body>
</html>
