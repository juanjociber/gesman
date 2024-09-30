function fnOcultarFondoMenu() {
	document.getElementById('button-menu').classList.replace('fa-times', 'fa-bars');
	document.querySelector('.navegacion').setAttribute('style', 'width: 0%; background: rgba(0,0,0,.0);');
	document.querySelectorAll('.navegacion .submenu').forEach(function (submenu) {
		submenu.setAttribute('style', 'left:-320px;');
	});
	document.querySelector('.navegacion .menu').setAttribute('style', 'left:-320px');
	document.getElementById('overlay').classList.toggle('active');
	return false;
}

function fnMostrarSubmenu(menu) {
	//idmenu = menu.parentNode.getAttribute('menu');
	//document.querySelector('.item-submenu[menu="' + idmenu + '"] .submenu').setAttribute("style", "left: 0px;");
	menu.parentNode.querySelector('.submenu').setAttribute("style", "left: 0px;");
	return false;
}

function fnOcultarSubmenu(menu) {
	menu.parentNode.setAttribute("style", "left: -320px;")
	return false;
}

function fnMenuToggle() {
	if (document.getElementById('button-menu').classList.contains('fa-bars')) {
		document.getElementById('button-menu').classList.replace('fa-bars', 'fa-times');
		document.querySelector('.navegacion .menu').setAttribute('style', 'left:0px;')
		document.getElementById('overlay').classList.toggle('active');
	} else {
		document.querySelector('.navegacion').setAttribute('style', 'width: 0%; background: rgba(0,0,0,.0);');
		document.getElementById('button-menu').classList.replace('fa-times', 'fa-bars');
		document.querySelectorAll('.navegacion .submenu').forEach(function (submenu) {
			submenu.setAttribute('style', 'left:-320px;');
		});
		document.querySelector('.navegacion .menu').setAttribute('style', 'left:-320px');
		document.getElementById('overlay').classList.toggle('active');
	}
	return false;
}