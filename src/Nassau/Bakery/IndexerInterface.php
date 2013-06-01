<?php

namespace Nassau\Bakery;


interface IndexerInterface
{
	public function rebuildIndex(ProjectInterface $project);
}