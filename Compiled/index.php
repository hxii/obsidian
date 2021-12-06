<?php defined("SOAP") || die("Invalid access"); ?>
<?php $ogp = new OGP() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="nofollow noarchive">
    <link rel="shortcut icon" href="favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="/logo-128.png">
    <link rel="stylesheet" href="/Templates/style.css">
    <link rel="author" href="humans.txt">
    <link rel="preload" href="/Templates/Fonts/PublicSans-Bold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/Templates/Fonts/PublicSans-BoldItalic.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/Templates/Fonts/PublicSans-Italic.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/Templates/Fonts/PublicSans-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="/Templates/style.css">
    <link rel="stylesheet" href="/Templates/fonts.css">
    <link rel="stylesheet" href="/Templates/icons.css">
    <?php $ogp->set('locale', 'en_US')->set('site_name', 'Diary.by')->set('title', BRAND_TEXT)->set('url', BRAND_URL) ?>
    <?php $ogp->set('description', BRAND_DESCRIPTION) ?>
    <?php if (isset($pagetype)): ?>
        <?php $ogp->set('url', Router::currentRoute()->current ) ?>
        <?php if ('list' === $pagetype || 'tag' === $pagetype): ?>

        <meta name="title" content="<?php echo Notes::cc(Settings::get('metaTitle'), $user['name'], $user['handle']) ?? '' ?>">
        <meta name="description" content="<?php echo Settings::get('metaDescription') ?? '' ?? '' ?>">
        <link rel="alternate" type="application/rss+xml" title="<?php echo $pagetitle ?? '' ?>" href="/~<?php echo $user['handle'] ?? '' ?>/rss">
        <?php $ogp->set('title', Notes::cc(Settings::get('metaTitle'), $user['name'], $user['handle'])) ?>
        <?php $ogp->set('description', Settings::get('metaDescription') ?? '' ) ?>
        
        <?php elseif ('entry' === $pagetype): ?>

        <meta name="title" content="<?php echo $title ?? '' ?>">
        <meta name="description" content="<?php echo $entry['description'] ?? '' ?>">
        <link rel="alternate" type="application/rss+xml" title="<?php echo $pagetitle ?? '' ?>" href="/~<?php echo $user['handle'] ?? '' ?>/rss">
        <meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:description" content="<?php echo $entry['description'] ?? '' ?>">
		<meta name="twitter:title" content="<?php echo $pagetitle ?? '' ?>">
		<meta name="twitter:image" content="<?php echo BRAND_URL . '/og/' . $user['handle'] . DS . $entry['slug'] ?? '' ?>">
        <?php $ogp->set('title', $pagetitle) ?>
        <?php $ogp->set('description', $entry['description'] ?? '' ) ?>
        <?php $ogp->set('image', BRAND_URL . '/og/' . $user['handle'] . DS . $entry['slug']) ?>
        <?php $ogp->set('type', 'article') ?>

        <?php endif; ?>
    <?php endif; ?>
    <?php $ogp->print() ?>
    <title><?php echo $pagetitle ?? BRAND_TEXT ?? '' ?></title>
</head>
<body>
    <main data-type="<?php echo $pagetype ?? 'default' ?? '' ?>">
        





<header>
    <div class="crumbs">
        <?php if (isset($pagetype)): ?>
            
    <?php if ($pagetype === 'list'): ?>
    <h1 class="title"><?php echo Notes::cc(Settings::get('metaTitle'), $user['name'], $user['handle']) ?? '' ?></h1>
    <?php elseif ($pagetype === 'tag'): ?>
    <h1 class="title"><a href="/~<?php echo $user['handle'] ?? '' ?>"><?php echo Notes::cc(Settings::get('metaTitle'), $user['name'], $user['handle']) ?? '' ?></a></h1> / #<?php echo $tag ?? '' ?>
    <?php elseif ($pagetype === 'entry'): ?>
    <h1 class="title"><a href="/~<?php echo $user['handle'] ?? '' ?>"><?php echo Notes::cc(Settings::get('metaTitle'), $user['name'], $user['handle']) ?? '' ?></a></h1>
    <?php endif; ?>

        <?php else: ?>
            <a href="/"><?php echo BRAND_LOGO ?? '' ?></a>
        <?php endif ?>
    </div>
    
<nav>
    <ul>
        <?php if (Session::isLoggedIn()): ?>
        <li><a href="/new">New</a></li>
        <li><a href="/~<?php echo Notes::$loggedInUser['handle'] ?? '' ?>">Home</a></li>
        <li><a href="/logout">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>

</header>
        <?php echo Notifications::show() ?? '' ?>



<article>
    <?php echo $content ?? '' ?>
    <!-- <?php $latest = Notes::getLatestEntries(); ?>

<?php if(is_array($latest)): ?>
<h2>Check out these latest entries</h2>
<ul id="latest-entries">
    <?php foreach ($latest as $entry => $entrydata): ?>
    <li>
        <a href="/~<?php echo $entrydata['handle'] ?? '' ?>/<?php echo $entrydata['slug'] ?? '' ?>"><?php echo $entrydata['title'] ?? '' ?></a> by <a href="/~<?php echo $entrydata['handle'] ?? '' ?>"><?php echo Notes::cc($entrydata['name'], $entrydata['handle']) ?? '' ?></a>.
    </li>
    <?php endforeach; ?>
</ul>
<span><a href="/help#discoverability">Want to appear here as well?</a></span>
<?php endif; ?>
 -->
</article>

    <footer>
        <hr>
        <nav>
            <section>
                
    <a href="<?php echo ROOT_URL ?? '' ?>"><?php echo BRAND_LOGO ?? '' ?></a>

            </section>
            <ul>
                <li><a href="/help">Help</a></li>
                <li><a href="/report">Report</a></li>
                <li><a href="/privacy">Privacy</a></li>
                <li><a href="/roadmap">Roadmap</a></li>
                <li><a href="/extra">Extra</a></li>
                <?php if (Session::isLoggedIn()): ?>
                <li><a href="/settings">Settings</a></li>
                <?php else: ?>
                <li><a href="/login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </footer>
    </main>
</body>
</html>