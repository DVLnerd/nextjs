<?php
	include('files/functions.php'); 
?>
<!DOCTYPE html>

<!-- Yandex.Metrika counter -->
<!-- /Yandex.Metrika counter -->

<html>
	<head>
		<title><?php echo $settings['GooglePlusURL']; ?></title>
		<meta name="description" content="Накрутка в инстаграм подписчиков, лайков, просмотров в youtube, комментариев, раскрутка вконтакте, накрутка в телеграм.">
		<meta name="keywords" content="Накрутка подписчиков лайков просмотров комментариев раскрутка продвижение Instagram, раскрутка в фейсбук">
		<meta charset="utf-8">
		<!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
		<meta name="viewport" content="width=device-width, initial-scale=0.9, maximum-scale=0.9" />
		<link rel="shortcut icon" href="favicon.ico">
		<!-- CSS -->
		<link rel="stylesheet" href="theme/css/bootstrap.min.css">
		<link rel="stylesheet" href="theme/css/style.css">
		<link rel="stylesheet" href="theme/css/style-responsive.css">
		<link rel="stylesheet" href="theme/css/animate.min.css">
		<link rel="stylesheet" href="theme/css/vertical-rhythm.min.css">
		<link rel="stylesheet" href="theme/css/owl.carousel.css">
		<link rel="stylesheet" href="theme/css/magnific-popup.css">
		<link rel="stylesheet" href="theme/css/datatables.min.css">
		<link href="https://fonts.googleapis.com/css?family=Jura" rel="stylesheet">
	</head>
	<body class="appear-animate">
		<!-- Подгрузка лоадера, не удалять! -->
		<div id="page-loader" class="page-loader">
			<div class="loader">Загрузка...</div>
		</div>
		<script type="text/javascript"> setTimeout(function(){$('#page-loader').fadeOut();}, 2000); </script>
		<div class="page" id="top">
			<nav class="main-nav js-stick">
				<div class="full-wrapper relative clearfix">
					<div class="nav-logo-wrap local-scroll">
						<a href="index.php" class="logo">
							<img src="<?php echo $settings['Logo']; ?>" style="height: 42px;margin-right: 0px;margin-top: -8px; ">
							<?php echo $settings['WebsiteQuote']; ?>
						</a>
					</div>
					<div class="mobile-nav">
						<i class="fa fa-bars"></i>
					</div>
					<div class="inner-nav desktop-nav">
						<ul class="clearlist">
								<?php
									if(!isset($_SESSION['auth'])) {
								?>
										<li>
											<a href="login.php"> <i class="fa fa-sign-in"></i>  Вход</a>
										</li>

								<?php
									} else {
										?>
											<li>
												<a class="mn-has-sub copy-api"> <i class="fa fa-user"></i> <?php echo $UserName; ?></a>
												<ul class="mn-sub mn-has-multi">
													<li class="mn-sub-multi">
														<ul>
															<li>
													<?php
														if($UserGroup == 'administrator')
															echo '<a href="admin/">Админочка</a>';
													?>
													<a href="settings.php">Настройки</a>
													<a href="referr-documentation.php">Партнерская программа</a>
													<a href="api-documentation.php">Документация API</a>
													<a href="logout.php">Выход</a>
															</li>
														</ul>
													</li>
												</ul>
											</li>
											<li>
												<a href="new-order.php"> <i class="fa fa-shopping-cart"></i>  Заказать</a>
											</li>
											<li>
												<a class="mn-has-sub copy-api"> <i class="fa fa-history"></i>  История заказов</a>
												<ul class="mn-sub mn-has-multi">
													<li class="mn-sub-multi">
														<ul>
															<li>
																<a href="all-orders.php"> <i class="fa fa-list-ol"></i> Все заказы</a>
																<a href="completed-orders.php"> <i class="fa fa-check"></i> Выполненные заказы</a>
																<a href="in-process-orders.php"> <i class="fa fa-cogs"></i> Выполняемые заказы</a>
																<a href="api-orders.php"> <i class="fa fa-key"></i> API заказы</a>
															</li>
														</ul>
													</li>
												</ul>
											</li>
											<li>
												<a href="services.php"> <i class="fa fa-list"></i> Список услуг</a>
											</li>
											<li>
											<a href="deposit.php"> <i class="fa fa-credit-card"></i> Баланс: <?php echo $UserBalance; ?> ₽</a>
											</li>
										<?php
									}
								?>
						</ul>
					</div>
				</div>
			</nav>
