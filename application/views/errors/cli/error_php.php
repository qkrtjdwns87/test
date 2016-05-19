<?defined('BASEPATH') OR exit('No direct script access allowed'); ?>

A PHP Error was encountered

Severity:    <?=$severity, "\n"; ?>
Message:     <?=$message, "\n"; ?>
Filename:    <?=$filepath, "\n"; ?>
Line Number: <?=$line; ?>

<?if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

Backtrace:
<?	foreach (debug_backtrace() as $error): ?>
<?		if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
	File: <?=$error['file'], "\n"; ?>
	Line: <?=$error['line'], "\n"; ?>
	Function: <?=$error['function'], "\n\n"; ?>
<?		endif ?>
<?	endforeach ?>

<?endif ?>
