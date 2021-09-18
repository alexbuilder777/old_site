<?
echo header("HTTP/1.0 404 Not Found");
//echo http_response_code(404);	
?>
<html>

<head>

<style>
body	{
	background: #F7F7F7;
	color: #2F2F2F;
	margin: 0;
	padding: 0;
}

.error_code {
	border: 2px solid white;
	height: 98vh;
	display: flex;
	flex-flow: column nowrap;
	align-content: center;
	align-items: center;
	justify-content: center;
}
.error_code__number {
	font-size: 144px;
	font-family: Times New Roman;
	padding: 0px 30px 30px 30px;
}
.error_code__message__link {
	color: #2F2F2F;
}

</style>	
	
</head>	
	
<body>

<div class="error_code">
	<div class="error_code__number">404</div>	
	<div class="error_code__message">Извините, страница, которую вы ищете, не существует или была перемещена.</div>
	<div class="error_code__message">Вернуться на <a href="/" class="error_code__message__link">Главную</a>.</div>
</div>	
	
</body>		
</html>	