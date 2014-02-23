<?php

$projects = `salt maccie pillar.item projects --out=json`;
$projects = json_decode($projects);
$projects = $projects->maccie->projects;

$collectProjects = array();

foreach($projects as $name => $project)
{
	$collectProject = (object) array(
		'name' => $name,
		'path' => $project->path,
		'homepath' => str_replace('/home/koen', '~', $project->path),
		'projects' => array()
	);

	preg_match("/Projects\/([a-zA-Z0-9-_]*)\/code/", $project->path, $match);
	$vendor = $match[1];

	foreach(array('node_modules', 'vendor') as $folder)
	foreach(glob($project->path.'/'.$folder.'/'.$vendor.'*'.($folder == 'vendor' ? '/*' : '')) as $dir)
	{
		//$dir = realpath($dir);
		$pkg = array_pop(explode('/', $dir));
		$collectProject->projects[] = (object) array(
			'name' => $vendor.'-'.$pkg,
			'path' => $dir,
			'homepath' => str_replace('/home/koen', '~', $dir)
		);
	}

	if($name !== 'trapps-data')
	{
		$collectProjects[] = $collectProject;
	}
}

$projects = array_reverse($collectProjects);

require './project.xml';
