<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.1/mustache.min.js"></script>
	<title>카카오톡-로그인</title>
	<script>
		$(document).ready(function(){
			loadMemberList();
		});
		function loadMemberList(){
			$.ajax({
				type: "post",
				url: "getMemberList.php",
				data: {},
				dataType: 'text',
				cache: false,
				async: false
			})
			.done(function( result ){
				//성공했을때
				let memberList = {"MEMBER": JSON.parse(result)};

				let output = Mustache.render($("#divMemberList").html(), memberList);
				$("#divMemberList").html(output);
			})
			.fail(function( result, status, error ){
				//실패했을때
				alert("에러발생" + error);
			});
		}
		
		function login() {
			if(!document.getElementById("ddlMemberList").value) {
				alert("아이디를 선택해 주세요");
				return false
			}
			document.frm.submit();
		}
	</script>
</head>
<body style="margin:0px">
	<div style="width:100%; display:inline-block; height:630px; padding:0px; margin:0px">
		<form name=frm method=post action="login_ok.php">
			<div style="text-align:center; width:100%; margin-top:50px" id="divMemberList">
				<select name="ddlMemberList" id="ddlMemberList">
					{{#MEMBER}}
						{{#alias}}
							<option value="{{memberCode}}">{{alias}}</option>
						{{/alias}}
					{{/MEMBER}}
				</select>
			</div>
		</form>

		<div style="text-align:center; width:100%; margin-top:20px">
			<button onclick="login();">로그인하기</button>
		</div>
	</div>
</body>
</html>