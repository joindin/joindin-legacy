var frisby = require('frisby');

var URL = 'http://joind.in/api';

frisby.globalSetup({ // globalSetup is for ALL requests
    request: {
      headers: { 'Content-type': 'application/json' }
    }
});

frisby.create('GET user lornajane')
  .post(URL + '/user',
    {request:{
      action:{
        type:"getdetail",
        data:{uid:"lornajane"}
      }
    }
  }, {json:true})
  .expectStatus(200)
  .expectHeader("content-type", "application/json")
  .expectJSONTypes('*', {
    ID: String,
    username: String,
    full_name: String,
    last_login: String
  })
  .expectJSON([{
    ID: '110',
    username: 'lornajane',
    full_name: 'Lorna Mitchell'
  }])
//  .inspectJSON()
.toss();
