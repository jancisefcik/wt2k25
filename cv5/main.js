// new ws connection
let ws = new WebSocket('ws://localhost:8081');

// dom refs
const nameForm = document.querySelector('.name-form');
const chatroom = document.querySelector('.chatroom');
const chatList = document.querySelector('.chat-list');
const chatForm = document.querySelector('.chat-form');

// name
let userName = 'anon';

// enter chatroom with name
nameForm.addEventListener('submit', (e) => {
  e.preventDefault();
  
  userName = nameForm.nickname.value;
  nameForm.classList.add('hidden');
  chatroom.classList.remove('hidden');
});

// send a new chat message
chatForm.addEventListener('submit', (e) => {
  e.preventDefault();

  let mssg = chatForm.mssg.value;
  ws.send(JSON.stringify({ userName, mssg }));
  chatForm.reset();
});

// output event to dom
const outputMessage = ({ data }) => {
  const { userName, mssg } = JSON.parse(data);

  let template = `
    <li>
      <div class='name'>${userName}</div>
      <div class='mssg'>${mssg}</div>
    </li>
  `;
  chatList.innerHTML += template;
};

// setup listener
ws.addEventListener('message', outputMessage);