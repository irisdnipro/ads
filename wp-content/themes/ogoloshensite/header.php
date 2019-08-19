<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php bloginfo('name'); ?></title>
	<link rel="shortcut icon" type="image/png" href="<?php bloginfo('template_url'); ?>/imgs/icon_2gg.png">
	<!-- <link rel="stylesheet" type="text/css" href="css/style.css"> -->
	<?php wp_head(); ?>
</head>
<body>
	<header>
		
	
	<!-- Панель навігації -->
	<div class="container">
	<nav class="page-navigation">
		<?php wp_nav_menu( array(
        'theme_location' => 'menu',
        'menu' => 'Primary menu',
        'menu_class' => 'menu'
        ) ); ?>
			<!-- <ul>
				<li class="li1"><img src="<?php bloginfo('template_url'); ?>/imgs/icon_gg.png" width="55"> </li>
				<li class="li2"><a href="#"> Оголошення <span>Дніпро</span></a></li>
				<li class="li3"><a href="#"> Подати</a></li>
				<li class="li4"><a href="#"> Товари</a></li> -->
		<!-- Мова другий рівень -->
				<!-- <li class="li5"><a href="#" class="str9660"> Мова</a>
					<ul>
                <li> <a href="#"><img src="<?php bloginfo('template_url'); ?>/imgs/ua.png" class="flag">Українська</a></li>
                <li><a href="index_eng.html"><img src="<?php bloginfo('template_url'); ?>/imgs/gb.png" class="flag">English</a></li>
                
            </ul>
				</li> -->
		<!-- /Мова другий рівень -->

			<!-- <li class="poshuk">
			<form action="#">
				<input type="text" placeholder="Пошук">
			</form>
		</li>
			<li class="li6"><img src="<?php bloginfo('template_url'); ?>/imgs/user.png" width="20"> </li>
				<li class="li7"><a href="html/form.html">Реєстрація</a></li>
				<li class="li8"><img src="<?php bloginfo('template_url'); ?>/imgs/exit.png" width="30"></li>
				<li class="li9"><a href="#">Вхід</a></li>
			</ul> -->
		
	</nav>
	</div>
	<!-- /Панель навигації -->
	</header>
	
	