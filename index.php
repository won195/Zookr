<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous">
  </script>
	<script>
		let MY_USER_ID = ""; //나의 고유값 (주민번호)
		let MY_NAME = "";
		let websocket = null;

		function connect() { //웹소켓에 연결
			if(!$.trim($("#myname").val())) {
				alert("접속자명을 입력해 주세요");
				$("#myname").focus();
				return false;
			}

			let my_user_name = $.trim($("#myname").val());

			websocket = new WebSocket("ws://localhost:8008");
			websocket.onmessage = function(e) {//서버로부터 메시지가 올 때, 이 이벤트가 실행
				let message = JSON.parse(e.data);
				
				switch (message.code) {
					case "my_user_id": // user_id가 전송됨
						MY_USER_ID = message.msg;
						sendMyName(my_user_name);
						// alert("user_id 받음 :" + MY_USER_ID);
						break;
					case "chat_message" :
						$("#divMsg").append(`
						<div>
						  ${message.sender_name}:${message.msg}
						</div>`);
						break;
					case "all_users" : // 전체 사용자를 받음
						let ALL_WS = JSON.parse(message.msg);
						$("#divAllUser").html("");
						ALL_WS.forEach(function(element, index){
							// element.name
							// element.user_id
							$("#divAllUser").append(`
								<div>
									${element.user_name}
								</div>
							`)
						});
						break;
				}
			}

			function sendMyName(sending_user_name) {
				MY_NAME = sending_user_name;
				let data = {"code":"connect_name","name":sending_user_name, "user_id":MY_USER_ID};
				websocket.send(JSON.stringify(data));

				$("#divPannel").html(`
				<input type="text" id="txtMessage" value="" placeholder="보낼 메시지를 입력하세요" style="font-size:18px; width:320px"
				onkeypress="javascript:if(event.keycode==13){sendMessage();return false;};return true;">
				<button style="font-size:18px; background-color:black; color:white" onclick="sendMessage();">보내기</button>
				`);
			}
		} 
		function sendMessage() {
			let message = $("#txtMessage").val();
			message = $.trim(message);
			if(!message) {
				alert("보낼 메시지를 입력해 주세요");
				$("#txtMessage").focus();
				return false;
			}
			let data = {"code":"send_message","name":MY_NAME, "user_id":MY_USER_ID, "msg":message};
			websocket.send(JSON.stringify(data));
		}
	</script>
</head>
<body>
	<div style="width:820px; height:620px; background-color:#d0edf7; padding-top:10px; margin:0px auto; margin-top:40px">
		<div style="width:800px; height:560px; background-color:white; margin-left:10px; margin-right:10px">
			<div style="width:100%; height:100%; display:flex;">
				<div style="flex-grow:1;">
					<div style="height:30px; line-height:30px; background-color:#eed" id="divMsg">
						&nbsp
					</div>
				</div>
				<div style="width:20%; height:100%; box-shadow: 0 0 0 1px #d0e0f7 inset;">
					<div style="width:100%; height:30px; line-height:30px; background-color:#033279; color:white; text-align:center">
						사용자 목록
					</div>
					<div style="width:100%; line-height:22px; font-size:15px; text-align:center" id="divAllUser">
					</div>
				</div>
			</div>
		</div>
		
		<div style="width=100%; height:50px; text-align:center; padding-top:15px" id="divPannel">
			<input type="text" id="myname" value="" placeholder="접속자명 입력" style="font-size:18px; width:120px"
			onkeypress="javascript:if(event.keycode==13){connect();return false;};return true;">
			<button style="font-size:18px; background-color:black; color:white" onclick="connect();">접속하기</button>
		</div>
	</div>
</body>
</html>


