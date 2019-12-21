<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Change this path to location of video files (or create symlink).
$conf['dir'] = '/var/www/videos';
// Change the title of collection
$conf['title'] = 'Videos';

$videos = array();

$conf['debug'] = 0;
$conf['xdg-start'] = 'vlc://';
$conf['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/';

if($conf['debug'] == 1) {
	$conf['xdg-start'] = '';
}

function scan_dir($dir) {
	global $reversed;
	$ignored = array('.', '..', '.svn', '.htaccess', 'index.php');

	$files = array();
	foreach (scandir($dir) as $file) {
		if (in_array($file, $ignored)) continue;
		$files[$file] = filemtime($dir . '/' . $file);
	}

	if($reversed == 1) {
		arsort($files);
	}

	$files = array_keys($files);

	return ($files) ? $files : false;
}

function deep_scan($dir) {
	global $videos, $conf;
	$files = scan_dir($dir);

	$local_dir = str_replace($conf['dir'].'/', '', $dir);

	foreach($files as $file) {
		if($file != '.' && $file != '..') {
			if(is_dir($dir.'/'.$file)) {
				if(strpos($file, 'ignored_folder') > 0) {

				} else {
					deep_scan($dir.'/'.$file);
				}
			} else {
				if(strpos($file, '.mp4') || strpos($file, '.wmv') || strpos($file, '.mkv') || strpos($file, '.avi') || strpos($file, '.mov') || strpos($file, '.flv')) {
					$videos[] = $conf['url'].$local_dir.'/'.$file;
				}
			}
		}
	}
}

function get_dirs($dir) {
	$files = scan_dir($dir);
	$dirs = array();
	foreach($files as $file) {
		if($file != '.' && $file != '..') {
			if(is_dir($dir.'/'.$file)) {
				$dirs[] = $file;
			}
		}
	}
	return $dirs;
}

if(isset($_GET['pls'])) {

	$pls = $_GET['pls'];

	if($conf['debug'] == 0) {
		header('Content-Type: audio/mpegurl');
		header('Content-Disposition: inline; filename="'.$pls.'.m3u"');
	}

	if($pls == 'random') {
		deep_scan($conf['dir']);
		shuffle($videos);
	} elseif($pls == 'all') {
		deep_scan($conf['dir']);
	} else {
		deep_scan($conf['dir'].'/'.$pls);
	}

	foreach ($videos as $video) {
		echo ''.$video.'
';
	}

	die();
}

?>

<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='UTF-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=no' />
	<title><?php echo $conf['title']; ?></title>
	<style>
  * { margin: 0; padding: 0; box-sizing: border-box; line-height: 1; }

  body { background-color: #000; padding: 15px 0; color: #eee; }

  a { 
    display: inline-block;
    color: #eee;
    text-decoration: none;
    font-size: 20px;
    transition: all linear 100ms;
    padding: 5px 5px;
  }

  a:hover { color: #000; border-radius: 4px; }

  .catalog-videos a:hover {
    background: #a0e6eb;
  }

  ul {
    display: flex;
    list-style: none;
    flex-wrap: wrap;
  }

  ul:not(:last-of-type) {
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-bottom: 1px solid #111;
  }

  li {
    flex: 0 0 33.3333%;
    padding: 3px 10px;
  }

  @media (max-width: 1024px) {
    li {
      flex: 0 0 50%;
      padding: 0 15px;
    }
  }

  @media (max-width: 600px) {
    ul {
      display: block;
    }
  }
  </style>
</head>
<body>

	<div class='wrap catalog-videos'>
		<ul>
			<?php
				echo '<li><a href="'.$conf['xdg-start'].$conf['url'].'pls/random">Random</a></li>';
				echo '<li><a href="'.$conf['xdg-start'].$conf['url'].'pls/all">All</a></li>';
			?>
		</ul>

		<ul>
			<?php
				$root_dirs = get_dirs($conf['dir']);

				foreach ($root_dirs as $key => $value) {
					echo '<li><a href="'.$conf['xdg-start'].$conf['url'].'pls/'.$value.'">'.$value.'</a></li>';
				}
			?>
		</ul>
	</div>

</body>
</html>
