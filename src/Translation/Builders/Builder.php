<?php
namespace Translation\Builders;
/**
 *
 * @author martin.bazik
 */
interface Builder
{
	function save($outputFile, $data = null);
}