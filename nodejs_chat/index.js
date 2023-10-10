const WebSocket = require("ws");
const ws = new WebSocket.Server({ port: 8008 });

let user_id = 0; //주민등록번호처럼 클라이언트에게 부여되는 고유한 값
let ALL_WS = []; //전체 유저들을 통제할 수 있도록 각 유저에 대한 websocket, user_id 저장
ws.on("connection", function connect(websocket, req){ // 웹소켓에 특정 클라이언트가 연결되었을 떄 실행
  user_id++;
  console.log("NEW USER CONNECT ("+user_id+")");
  ALL_WS.push({"ws":websocket, "user_id":user_id, "user_name":""});

  sendUserId(user_id);
  websocket.on("message", function incoming(message){
    console.log(JSON.parse(message));
    message = JSON.parse(message);
    switch(message.code) {
      case "connect_name" : // 사용자 추가
        ALL_WS.forEach(function(element, index){
          if(element.user_id == message.user_id) {
            element.user_name = message.name;
          }
        });
        sendAllUsers();
        break ;
      case "send_message" : // 채팅메시지 받음 
        ALL_WS.forEach(function(element, index){
          // element.ws 클라이언트와의 연결 지점
          let data = {"code": "chat_message", "msg":message.msg, "sender_name":message.name};
          element.ws.send(JSON.stringify(data));
        });

        break;
    }
  });

  function sendAllUsers() { // 전체 사용자 정보를 보냄
    let data = {"code":"all_users","msg":JSON.stringify(ALL_WS)};

    ALL_WS.forEach(function(element, index){
      element.ws.send(JSON.stringify(data));
    });
  }
  
  function sendUserId(user_id) {
    let data = {"code":"my_user_id","msg":user_id};
    websocket.send(JSON.stringify(data));
  }
});