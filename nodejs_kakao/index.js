const WebSocket = require("ws");
const ws = new WebSocket.Server({port:8008}); 

let ALL_USER = [];
ws.on("connection", function connection(websocket, req){ //웹소켓에 특정 클라이언트가 연결되었을 떄, 실행되는 부분

  websocket.on("message", function incoming(message){ //클라이언트로 부터 특정 메시지가 도착하면, 실행되는 부분
    console.log(JSON.parse(message));
    message = JSON.parse(message);

    switch(message.code) {
      case "member_login"://로그인시에
        login(message.memberCode, message.memberAlias);
      break;
    }
  });
  
  function login(memberCode, memberAlias) {
    let member_data = {"memberCode": memverCode, "memberAlias": memberAlias, "ws": websocket}
    ALL_USER.push(member_data);
    console.log("LOGIN OK");
  }
});