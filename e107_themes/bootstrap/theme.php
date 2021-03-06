<?php
if ( ! defined('e107_INIT')) { exit(); }
/*
 * This is a 100% Pure Bootstrap Theme for e107 v2 
 */
define("BOOTSTRAP", 3); 
define("FONTAWESOME", 4);
define("VIEWPORT", "width=device-width, initial-scale=1.0");
define("BODYTAG", '<body data-spy="scroll" data-target=".bs-docs-sidebar" >');

e107::lan('theme');

define("CSSORDER", "theme,core,other,plugin,inline"); // TODO try to avoid needing this. - corrects font-awesome overlap issue. 

if(THEME_STYLE != 'style.css') // allow for drop-in bootstrap replacement. See http://bootswatch.com
{
	

	switch (THEME_STYLE) 
	{
		case 'css/superhero.css':
			e107::css('inline','@media (min-width: 768px) and (max-width: 992px) { .container-default, .container-hero { margin-top: 125px;  }  } ');	
		break;

		case 'css/united.css':
			e107::css('inline','@media (min-width: 768px) and (max-width: 992px) { .container-default, .container-hero { margin-top: 50px;  }  } ');	
		break;
		
		default:
			e107::css('inline','@media (min-width: 768px) and (max-width: 992px) { .container-default, .container-hero { margin-top: 125px;  }  } ');	
		break;
	}	
}
else
{
	e107::css('url', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
        e107::css('inline','@media (min-width: 768px) and (max-width: 992px) { .container-default, .container-hero { margin-top: 125px;  }  } ');

	 }
	
	 ');
}

e107::css('bootstrap','jquery-ui.custom.css');
e107::css('url', "https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css");

e107::css('theme', 'js/google-code-prettify/prettify.css');
e107::js('theme', "js/google-code-prettify/prettify.js");
e107::css('page', 'css/page.navigation.css', 'jquery');
e107::js("url", "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js");


if(THEME_LAYOUT == 'docs')
{
	e107::css('inline','body { padding-top: 40px }');
	e107::css('theme','css/docs.css');	
	e107::js('theme', "js/holder/holder.js");
    e107::js('theme', "js/application.js");
}



//$no_core_css = TRUE;

//define("STANDARDS_MODE",TRUE);


/*
$OTHERNEWS_STYLE = '<div class="col-md-4">
              		<h2>{NEWSTITLE}</h2>
              		<p>{NEWSSUMMARY}</p>
              		<p><a class="btn" href="{NEWSURL}">View details &raquo;</a></p>
            		</div><!--/span-->';


$OTHERNEWS2_STYLE = '<div class="col-md-4">
              		<h2>{NEWSTITLE}</h2>
              		<p>{NEWSSUMMARY}</p>
              		<p><a class="btn" href="{NEWSURL}">View details &raquo;</a></p>
            		</div><!--/span-->';
*/
					
define('OTHERNEWS_COLS',false); // no tables, only divs. 
define('OTHERNEWS_LIMIT', 3); // Limit to 3. 
define('OTHERNEWS2_COLS',false); // no tables, only divs. 
define('OTHERNEWS2_LIMIT', 3); // Limit to 3. 

define('PRE_EXTENDEDSTRING', '<br />');


function tablestyle($caption, $text, $mode='') 
{
	global $style;
	
	$type = $style;
	if(empty($caption))
	{
		$type = 'box';
	}
	
	if($style == 'navdoc')
	{
		echo $text;
		return;
	}
		
		
	
	if($mode == 'wm') // Welcome Message Style. 
	{
		
		echo '<div class="hero-unit jumbotron">
            <h1>'.$caption.'</h1>
            <p>'.$text.'</p>
          </div>';	
		
		return;
	}
	
	if($mode == 'loginbox') // Login Box Style. 
	{
		 echo '<div class="well sidebar-nav">
		 <ul class="nav nav-list"><li class="nav-header">'.$caption.'</li></ul>
		 
           '.$text.'
			
        </div><!--/.well -->';
          return;
	}
			
	if($mode == 'login_page')
	{
		$type = 'no_caption';	
	}
	
	switch($type) 
	{
		// Default Menu/Side-Panel Style
		case 'menu' :
			echo '<div class="well sidebar-nav">
		 <ul class="nav nav-list"><li class="nav-header">'.$caption.'</li></ul>
		 
           '.$text.'
			
        </div><!--/.well -->';
		break;
		
		case 'span4' :
			echo $text; 
		break;
		
		case 'box':
			echo '
				<div class="block">
					<div class="block-text">
						'.$text.'
					</div>
				</div>
			';
		break;
		
		case 'no_caption':
			echo $text;
		break;
	
		default: // Main Content Style. 
			echo '
				<h2>'.$caption.'</h2>
					<p>
						'.$text.'
					</p>
				
			';
		break;
	}
}

$SC_WRAPPER['NAVIGATION|s'] = '<div class="well sidebar-nav">{---}</div><!--/.well -->'; 

// TODO Convert to : default-home and default-other layouts. 


//// <ul class="nav nav-pills pull-right">
//                <li class="dropdown">'.(!USERID ? '<a class="dropdown-toggle" role="button" href="'.e_LOGIN.'">Sign in</a>': '<span class="navbar-text">Logged in as</span> <a class="dropdown-toggle no-block" role="button" href="user.php?id.'.USERID.'">'.USERNAME.'</a>').'</li>
//            </ul>


$HEADER['default'] = '
<nav class="navbar navbar-inverse navbar-fixed-top"  role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="'.SITEURL.'">{SITENAME}</a>
        </div>
        <div id="navbar" class="nav-collapse collapse">
           {NAVIGATION=main}
           {BOOTSTRAP_USERNAV}

        </div>

    </div>
</nav>
<div class="container-fluid container-default">
	<div class="row">
		 <div class="col-md-3">
           {NAVIGATION|s=side}          
          {SETSTYLE=menu}
          {MENU=1}
        </div><!--/span-->
		<div class="col-md-9">
		 {SETSTYLE=default}
		 	{WMESSAGE}
';



$FOOTER['default'] = '
		 {SETSTYLE=span4}
      	
		</div><!--/span-->
	</div><!--/row-->

<hr>

<div class="row">
    <footer class="center"> 
	    {SITEDISCLAIMER}
    </footer>
</div>

</div><!--/.fluid-container-->';


$HEADER['default-home'] = $HEADER['default'];


$FOOTER['default-home'] = '
	
		 {SETSTYLE=span4}
		 
		 <div class="row">
            {MENU=2}
      	</div><!--/row-->
		 <div class="row">
            {MENU=3}
      	</div><!--/row-->	
      	
		</div><!--/span-->
	</div><!--/row-->

<hr>
<div class="row">
    <footer class="center"> 
		{SITEDISCLAIMER} 
    </footer>
</div>

</div><!--/.fluid-container-->';










// HERO http://twitter.github.com/bootstrap/examples/hero.html
//FIXME insert shortcodes while maintaining only bootstrap classes. 

$HEADER['hero'] = '

<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container container-hero">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Project name</a>
        </div>
        <div id="navbar" class="nav-collapse collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#">About</a></li>
              <li><a href="#">Contact</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li role="separator" class="divider"></li>
                  <li class="dropdown-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
            </ul>
            <form class="navbar-form navbar-right">
              <input class="col-md-2" type="text" placeholder="Email">
              <input class="col-md-2" type="password" placeholder="Password">
              <button type="submit" class="btn">Sign in</button>
            </form>
          </div><!--/.nav-collapse -->
        </div>

    </div>

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit jumbotron">';
	  
	  /*
        <h1>Hello, world!</h1>
        <p>This is a template for a simple marketing or informational website. It includes a large callout called the hero unit and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
        <p><a class="btn btn-primary btn-large">Learn more &raquo;</a></p>
     */
     
     
//FIXME insert shortcodes while maintaining classes. 
$FOOTER['hero'] = '
 </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4>
          <h2>Heading</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
        <div class="col-md-4">
          <h2>Heading</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
       </div>
        <div class="col-md-4">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
      </div>

      <hr>

      <footer class="col-md-12">
        <p>&copy; Company 2012</p>
      </footer>

    </div> <!-- /container -->';


// Marketing Narrow - http://twitter.github.com/bootstrap/examples/marketing-narrow.html
//FIXME insert shortcodes while maintaing classes. 

$HEADER['marketing-narrow'] = '
 <div class="container container-narrow">

      <div class="masthead">
        <ul class="nav nav-pills navbar-right">
          <li class="active"><a href="#">Home</a></li>
          <li><a href="#">About</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
        <h3 class="muted">Project name</h3>
      </div>

      <hr>

      <div class="jumbotron">
        <h1>Super awesome marketing speak!</h1>
        <p class="lead">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
        <a class="btn btn-large btn-success" href="#">Sign up today</a>   
';


//FIXME insert shortcodes while maintaing classes. 

$FOOTER['marketing-narrow'] = '
</div>

      <hr>

      <div class="row marketing">
        <div class="col-md-6">
          <h4>Subheading</h4>
          <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

          <h4>Subheading</h4>
          <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

          <h4>Subheading</h4>
          <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
        </div>

        <div class="col-md-6">
          <h4>Subheading</h4>
          <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

          <h4>Subheading</h4>
          <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

          <h4>Subheading</h4>
          <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
        </div>
      </div>

      <hr>

      <div class="footer col-md-12">
        <p>&copy; Company 2012</p>
      </div>

    </div> <!-- /container -->
    
';







/*

	$CUSTOMHEADER, CUSTOMFOOTER and $CUSTOMPAGES are deprecated.
	Default custom-pages can be assigned in theme.xml

 */

 
 
$HEADER['docs'] = <<<TMPL

  <!-- Navbar
    ================================================== -->
<div class="navbar navbar-inverse navbar-fixed-top">
     <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="./index.html">Bootstrap</a>



        </div>
        <div id="navbar" class="navbar-collapse collapse">
            {NAVIGATION=main}
        </div>
     </div>
</div>

<!-- Subhead
================================================== -->
<header class="jumbotron subhead" id="overview">
  <div class="container">
    <h1>{PAGE_CHAPTER_NAME}</h1>
    <p class="lead">{PAGE_CHAPTER_DESCRIPTION}</p>
  </div>
</header>


  <div class="container">

    <!-- Docs nav
    ================================================== -->
    <div class="row">
    
      <div class="col-md-3 bs-docs-sidebar">
      {SETSTYLE=navdoc}
	  {PAGE_NAVIGATION: template=navdocs&auto=1}
      </div>
		{SETSTYLE=doc}
	  
      <div class="col-md-9">





TMPL;


$FOOTER['docs'] = <<<TMPL
 	</div>
	  </div>
 </div>

    <!-- Footer
    ================================================== -->
    <footer class="footer col-md-12">
      <div class="container">
        <p>{SITEDISCLAIMER}</p>
        <!--
        <ul class="footer-links">
          <li><a href="http://blog.getbootstrap.com">Blog</a></li>
          <li class="muted">&middot;</li>
          <li><a href="https://github.com/twitter/bootstrap/issues?state=open">Issues</a></li>
          <li class="muted">&middot;</li>
          <li><a href="https://github.com/twitter/bootstrap/blob/master/CHANGELOG.md">Changelog</a></li>
        </ul>
        -->
      </div>
    </footer> 
 
 
 
TMPL;
 
 
 
 
 
 
$NEWSCAT = "\n\n\n\n<!-- News Category -->\n\n\n\n
	<div style='padding:2px;padding-bottom:12px'>
	<div class='newscat_caption'>
	{NEWSCATEGORY}
	</div>
	<div style='width:100%;text-align:left'>
	{NEWSCAT_ITEM}
	</div>
	</div>
";


$NEWSCAT_ITEM = "\n\n\n\n<!-- News Category Item -->\n\n\n\n
		<div style='width:100%;display:block'>
		<table style='width:100%'>
		<tr><td style='width:2px;vertical-align:middle'>&#8226;&nbsp;</td>
		<td style='text-align:left;height:10px'>
		{NEWSTITLELINK}
		</td></tr></table></div>
";



?>
