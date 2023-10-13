<?php
	include "conn.php";

	if(isset($_SESSION["kakao_member_code"]) == false || !$_SESSION["kakao_member_code"]) { //로그인하지 않았을 때
		?>
			<script>
				location.replace("login.php");
			</script>
		<?php
		exit;
	}
  // $_SESSION["kakao_member_code"] = $ddlMemberList;
	// $_SESSION["kakao_member_alias"] = $alias;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
	<title>카카오톡</title>
	<style>
		.divFriendTr {
			height:33px;
			display:inline-block;
			line-height:33px;
			vertical-align:middle;
			padding-top: 6px;
			padding-bottom: 6px;
			padding-left: 14px;
			margin: 0px;
			width:calc(100% - 14px);
			clear:both;
		}

		.divChatTr{
			min-height:33px;
			display:inline-block;
			line-height:33px;
			vertical-align:middle;
			padding-top:6px;
			padding-bottom:6px;
			padding-right:30px;
			margin:0px;
			width:calc(100% - 30%);
			float:right;
			clear:both;
		}
		
		.divChatTrMy{
			min-height:33px;
			display:inline-block;
			line-height:33px;
			vertical-align:6px;
			padding-top:6px;
			padding-bottom:6px;
			padding-right:30px;
			margin:0px;
			width:calc(100% - 30px);
			float:right;
			clear:both;
			font-size:13px;
		}
	</style>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.1/mustache.min.js"></script>
	<script>
		let websocket = null; //웹소켓을 통해 서버와 연결
		let NEW_ROOM_ID = ""; //현재 접속중인 방 코드 정보 

		$(document).ready(function(){
			connect();
			loadMemberList();
		});

		function connect() {
			websocket = new WebSocket("ws://172.30.1.254:8008");
			websocket.onopen = function(e){
				let data = {"code": "member_login", "memberCode": "<?php echo $_SESSION["kakao_member_code"]?>","memberAlias": "<?php echo $_SESSION["kakao_member_alias"]?>"}
				sendMessage(data);
			}
			websocket.onmessage = function(e) {
				let message = JSON.parse(e.data;)

				switch(message.code) {
					case "send_roominfo" : // 방 생성후 받은 방 코드 정보가 있음
						NOW_ROOM_ID = message.room_id;

						getAllMessageFromRoom(NOW_ROOM_ID, "first");
					break;
					case "arrive_new_message" : // 새 메시지 도착
						NOW_ROOM_ID = message.room_id;

						getAllMessageFromRoom(NOW_ROOM_ID, "notfirst");
					break;
				}
			}
		}

		function getAllMessageFromRoom(room_id, mode) {
			$.ajax({
				type: "POST",
				url: "getAllMessageFromRoom.php",
				data: {"room_id":room_id},
				dataType: 'text',
				cache: false,
				async: false
			})
			.done(function( result ){
				let chatList = {"CHAT": JSON.parse(result)};
				chatList.CHAT.forEach(function(element, index){
					let isMy = false;
					let isYou = true;
					if(element.memberCode == <?php echo $_SESSION["kakao_member_code"]?>){
						isMy = true;
						isYou = false;
					}

					element.chat_contents = chatList.CHAT[index].chat_contents.replace(/(?:\r\n|\r|\n)/g, '<br />');
					chatList.CHAT[index]is.My = is.My;
					chatList.CHAT[index]is.You = is.You;
				});

				if(mode == "first"){
					let output = Mustache.render($("#MAIN").html(), chatList);
					$("#MAIN" ).html(output);
					$("#MAIN_CONTENTS").scrollTop($("#MAIN_CONTENTS")[0].scrollHeight);
				}
				else {
					$("#BACKGROUND").load("chat.php", function() {
						let chat_name = $("#spanChatName").html();
						let output = Mustache.render($("#BACKGROUND").html(), chatList);
						$("#MAIN" ).html(output);
						$("#spanChatName").html(chat_name);
						
						$("#BACKGROUND").html("");
						$("#MAIN_CONTENTS").scrollTop($("#MAIN_CONTENTS")[0].scrollHeight);
					});
				}
			})
			.fail(function( result, status, error ){
				//실패했을때
				alert("에러발생" + error);
			});
		}

		function sendMessage(msg) { //메시지 전송 역할
			websocket.send(JSON.stringify(msg));
		}

		function loadMemberList() {
			$('#MAIN').css("left", (0 - $(document).width()));
			$('#MAIN').load("member.php", function() {
				$('#MAIN').animate({left:0, top:0});

			//1. 데이터베이스의 회원 정보를 읽어 json 객체 형태로 받는 것
			$.ajax({
				type: "POST",
				url: "getMemberList.php",
				data: {},
				dataType: 'text',
				cache: false,
				async: false
			})
			.done(function( result ){
				//성공했을때
				let memberList = {"MEMBER": JSON.parse(result)};

			//2. 받은 내용을 무스타크로 출력하는 것
				let output = Mustache.render($("#divMemberList").html(), memberList);
				$("#divMemberList").html(output);
			})
			.fail(function( result, status, error ){
				//실패했을때
				alert("에러발생" + error);
			});
			});
		}

		function openChat(you_member_code, you_member_alias){
			let members = [];
			let me = {"memberCode": "<?php echo $_SESSION["kakao_member_code"]?>","memberAlias": "<?php echo $_SESSION["kakao_member_alias"]?>",}
			members.push(me);

			if ("<?php echo $_SESSION["kakao_member_code"]?>" != you_member_code) {
				let you = {"memberCode": you_member_code,"memberAlias": you_member_alias};
				members.push(you);
		  }

			$('#MAIN').css("left", ($(document).width() + 100));
			$('#MAIN').load("chat.php", function() {
				$('#MAIN').animate({left:0, top:0});
				
				let chat_name = getChatName(members);
				$("#spanChatName").html(chat_name);

				let data = {"code":"create_room", "members": members };
				sendMessage(data);
			});
		}

		function getChatName(members) {
			let return_values = "";
			members.forEach(function(element, index) {
				if(!return_value) {
					return_value = element.memberAlias;
				}
				else{
					return_value += "," + element.memberAlias;
				}
			});
			return return_value;
		}

		function sendChat() {
			let chat_message = $("#chat_message").val();
			$.ajax({
				type: "POST",
				url: "chat_message_insert.php",
				data: {"room_id":NOW_ROOM_ID,"chat_message": chat_message},
				dataType: 'text',
				cache: false,
				async: false
			})
			.done(function( result ){
				if(result == "OK") { //성공한경우
					let data = {"code": "send_chat", "room_id": NOW_ROOM_ID, "send_memberCode": "<?php echo $_SESSION["kakao_member_code"]?>"};
					sendMessage(data);
				}
			})
			.fail(function( result, status, error ){
				//실패했을때
				alert("에러발생" + error);
			});
			};
		
	</script>
</head>
<body style="margin:0px">
	<div style="width:100%; display:inline-block; height:630px; padding:0px; margin:0px; position:relative; left:0px; top:0px" id="MAIN">

	</div>
	<div style="width:0%; height:0px; padding:0px; margin:0px; position:relative; left:0px; top:0px" id="BACKGROUND">

	</div>
</body>
	</html>