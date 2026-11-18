<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="google-site-verification" content="KamfaQ0k0vBLnnJTvuiAl6BYDqPvM8UuqhwmHeVXWhk" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php if (!empty($pageDescription)): ?>
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>" />
    <?php endif; ?>
    <title><?= htmlspecialchars($pageTitle ?? 'T1 Health &amp; Wellness', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" href="images/logo.png" type="image/png" />
    <link rel="icon" type="image/x-icon" href="images/favicon.ico" />

    <!-- Shared site styles -->
    <link rel="stylesheet" href="styles/styles.css" />
    <?php if (!empty($pageStylesheet)): ?>
    <link rel="stylesheet" href="styles/<?= htmlspecialchars($pageStylesheet, ENT_QUOTES, 'UTF-8') ?>" />
    <?php endif; ?>

    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css"
      integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <?= $pageHeadExtra ?? '' ?>
  </head>
  <body id="<?= htmlspecialchars($bodyId ?? 'top', ENT_QUOTES, 'UTF-8') ?>" class="<?= htmlspecialchars($bodyClass ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <header class="site-header">
