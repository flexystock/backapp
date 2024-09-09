<?php

use Doctum\Doctum;
use Doctum\RemoteRepository\GitHubRemoteRepository;
use Doctum\Version\GitVersionCollection;

$dir = __DIR__.'/src';

$versions = GitVersionCollection::create($dir)
    ->addFromTags('v1.*')
    ->add('master', 'Development branch');

$repo = new GitHubRemoteRepository('flexystock/backapp', $dir);

return new Doctum($dir, [
    'versions'            => $versions,
    'title'               => 'FlexyStock API Documentation',
    'build_dir'           => __DIR__.'/docs/api/%version%',
    'cache_dir'           => __DIR__.'/cache/doctum/%version%',
    'remote_repository'   => $repo,
]);

