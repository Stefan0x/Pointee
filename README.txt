/*
	Copyright (c) 2010 Iain Urquhart - shout@iain.co.nz

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
 */
 

ABOUT

Pointee stores the x & y coordinates of a users click on an image. (think maps, or image tagging)

The image can be pre-defined per field, or the user can upload an image per publish using the EE filemanager.

EXAMPLE USAGE

{exp:channel:entries channel="my_channel"}
<h3>{title}</h3>

<table>
	<tr>
		<th colspan="2">Field example with image uploaded/selected per entry</th>
	</tr>
	<tr>
		<th scope="row">X</th><td>{my_field show="x" offset="-5"}</td>
	</tr>
	<tr>
		<th scope="row">Y</th><td>{my_field show="y" offset="10"}</td>
	</tr>
	<tr>
		<th scope="row">Image</th><td>{my_field show="image"}</td>
	</tr>
	<tr>
		<th scope="row">Raw Tag</th><td>{my_field}</td>
	</tr>
</table>

<table>
	<tr>
		<th colspan="2">Field example with image defined by field settings</th>
	</tr>
	<tr>
		<th scope="row">X</th><td>{my_field show="x" offset="5"}</td>
	</tr>
	<tr>
		<th scope="row">Y</th><td>{my_field show="y" offset="-5"}</td>
	</tr>
	<tr>
		<th scope="row">Raw Tag</th><td>{my_field}</td>
	</tr>
</table>

<hr />
{/exp:channel:entries}




