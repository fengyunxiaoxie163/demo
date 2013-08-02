{template:header}
	<link rel="alternate" type="application/rss+xml" href="{$host}feed.php" title="{$name}" />
</head>
<body class="multi default">
<div id="divAll">
	<div id="divPage">
	<div id="divMiddle">
		<div id="divTop">
			<h1 id="BlogTitle"><a href="{$host}">{$name}</a></h1>
			<h3 id="BlogSubTitle">{$subname}</h3>
		</div>
		<div id="divNavBar">
<ul>
{$modules['navbar'].Content}
</ul>
		</div>
		<div id="divMain">

{template:post-page}

		</div>
		<div id="divSidebar">
{template:sidebar}
		</div>
{template:footer}