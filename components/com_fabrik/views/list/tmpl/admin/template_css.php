<?php
header('Content-type: text/css');
$c = $_REQUEST['c'];
echo "
#listform_$c .sequenceName{
	color:#6022E5;
}

#listform_$c td.decimal,
#listform_$c td.integer{
	text-align:right;
}

#listform_$c .oddrow0{
	background-color:#FAFAFA;
}

#listform_$c .oddrow1{
	background-color:#Efefef;
}

#listform_$c ul.fabrikRepeatData {
	padding: 0;
	margin: 0;
	list-style: none;
}

#listform_$c ul.fabrikRepeatData li {
	background-image: none;
	padding: 0;
	margin: 0;
	min-height: 20px;
}

#listform_$c .fabrikList tfoot{
	background-color:#F3F3F3;
	border-top:1px solid #999999;
	text-align:center;
}

#listform_$c .list-footer{
	display:inline-block;
}

#listform_$c .counter{
	clear:left;
	padding-top:1em;
}

#listform_$c .list-footer div.limit {
	float: left;
	height: 22px;
	line-height: 22px;
	margin: 0 10px;
}
/*****************************************************/
/********** default action formatting ****************/
/*****************************************************/

#listform_$c .fabrik_row ul.fabrik_action,
#listform_$c ul.fabrik_action{
	list-style:none !important;
	border:1px solid #999;
	padding:0;
	text-align:left;
	font-weight: bold;
	font-size: 11px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
	height:23px;
	margin:5px;
		filter: progid : DXImageTransform.Microsoft.gradient ( startColorstr =
		'#cccccc', endColorstr = '#666666' ); /* for IE */
	background: -webkit-gradient(linear, left top, left bottom, from(#ccc),
		to(#666) ); /* for webkit browsers */
	background: -moz-linear-gradient(top, #eee, #ccc);
	display:-moz-box;
	/*display:-webkit-box;*/
	display:box;
	float:right;
}

#listform_$c .fabrik_row ul.fabrik_action span,
#listform_$c ul.fabrik_action span{
	display:none;
}

#listform_$c .fabrik_row .fabrik_action li,
#listform_$c .fabrik_action li{
	float:left;
	padding:2px 6px 0 6px;
	border-left:1px solid #999;
	min-height:17px;
	margin-top:2px;
	margin-bottom:2px;
}

#listform_$c .fabrik_row .fabrik_action li:first-child,
#listform_$c .fabrik_action li:first-child{
-moz-border-radius: 6px 0 0 6px;
	-webkit-border-radius: 6px 0 0 6px;
	border-radius: 6px 0 0 6px;
	border:0;
}

#listform_$c .fabrik_row .fabrik_action li:last-child,
#listform_$c .fabrik_action li:last-child{
-moz-border-radius: 0 6px 6px 0;
	-webkit-border-radius: 0 6px 6px 0;
	border-radius: 0 6px 6px 0;
}

/*****************************************************/
/********** the context menu that appears ************/
/********** when you click row checkboxes ************/
/*****************************************************/

.floating-tip a:link,
.floating-tip a:visited,
.floating-tip a{
	text-decoration:none;
}
.floating-tip {
	background-color: #fff;
	border:1px solid #333;
	padding:0;
	text-align:left;
	font-weight: bold;
	font-size: 11px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
	list-style:none !important;
	box-shadow: 2px 2px 2px #aaa;
	-moz-box-shadow: 2px 2px 2px #aaa;
	-webkit-box-shadow: 2px 2px 2px #aaa;
}
.floating-tip li:first-child{
	-moz-border-radius: 6px 6px 0 0;
	-webkit-border-radius: 6px 6px 0 0;
	border-radius: 6px 6px 0 0;
}

.floating-tip li:last-child{
	-moz-border-radius: 0 0 6px 6px;
	-webkit-border-radius: 0 0 6px 6px;
	border-radius: 0 0 6px 6px;
}

.floating-tip li{
	min-height:19px;
	border-top:1px solid #D7D7D7;
	padding:5px 16px;
	background-color:#fff;
}

.floating-tip li:hover{
	background-color:#DBF5FA;
}

.floating-tip li span{
	margin-left:7px;
}
";?>