#!/usr/bin/php -q
<?php

/**
 * php-msgfmt
 *
 * php-msgfmt is dual-licensed under
 *   the GNU Lesser General Public License, version 2.1, and
 *   the Apache License, version 2.0.
 * 
 * For the terms of the licenses, see the LICENSE-LGPL.txt and LICENSE-AL2.txt
 * files, respectively.
 *
 *
 * Copyright (C) 2007 Matthias Bauer
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License, version 2.1, as published by the Free Software Foundation.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * Copyright 2007 Matthias Bauer
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package php-msgfmt
 * @version $Id$
 * @copyright 2007 Matthias Bauer
 * @author Matthias Bauer
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License 2.1
 * @license http://opensource.org/licenses/apache2.0.php Apache License 2.0
 */

/*
 * This is a command line PHP script to compile gettext PO files into gettext MO files.
 * Output should be equivalent to that of msgfmt --no-hash
 *  
 * Syntax: ./msgfmt.php file.po [-o output.mo]
 */

include (dirname(__FILE__) . '/msgfmt-functions.php');

array_shift($argv);
$in= array_shift($argv);
$out= str_replace('.po', '.mo', $in);
if (array_shift($argv) == '-o')
	$out= array_shift($argv);

$hash= parse_po_file($in);
if ($hash === FALSE) {
	print("Error reading '{$in}', aborted.\n");
}
else {
	write_mo_file($hash, $out);
}

?>
