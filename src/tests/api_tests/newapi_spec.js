// vim: tabstop=2:softtabstop=2:shiftwidth=2 
// ./node_modules/.bin/jasmine-node . 
var frisby = require('frisby');
var util   = require('util');

var baseURL;
if (typeof process.env.JOINDIN_API_BASE_URL != 'undefined') {
	baseURL = process.env.JOINDIN_API_BASE_URL;
} else {
	baseURL = "http://api.dev.joind.in:8080";
}

frisby.globalSetup({ // globalSetup is for ALL requests
  request: {
    headers: { 'Content-type': 'application/json' }
  }
});

frisby.create('Initial discovery')
  .get(baseURL)
  .expectStatus(200)
  .expectHeader("content-type", "application/json; charset=utf8")
  .expectJSON({
    'events'          : baseURL + '/v2.1/events',
    'hot-events'      : baseURL + '/v2.1/events?filter=hot',
    'upcoming-events' : baseURL + '/v2.1/events?filter=upcoming',
    'past-events'     : baseURL + '/v2.1/events?filter=past',
    'open-cfps'       : baseURL + '/v2.1/events?filter=cfp'
  })

  .afterJSON(function(apis) {

    // Loop over all of the event types
    for (var evType in apis) {

      frisby.create('Event list for ' + evType)
        .get(apis[evType])
        .expectStatus(200)
        .expectHeader("content-type", "application/json; charset=utf8")
        .afterJSON(function(ev) {
          // Check meta-data
          expect(ev.meta).toContainJsonTypes({"count":Number});
          expect(ev).toContainJsonTypes({"events":Array});

          for(var i in ev.events) {
            checkEvent(ev.events[i]);
  		
          // Check for more detail in the events
            frisby.create('Event detail for ' + ev.events[i].name)
              .get(ev.events[i].verbose_uri)
              .expectStatus(200)
              .expectHeader("content-type", "application/json; charset=utf8")
              .afterJSON(function(detailedEv) {
                expect(detailedEv.events[0]).toBeDefined();
                expect(typeof detailedEv.events[0]).toBe('object');
                var evt = detailedEv.events[0];
                checkVerboseEvent(evt);

                frisby.create('Event comments for ' + evt.name)
                  .get(evt.comments_uri)
                  .expectStatus(200)
                  .expectHeader("content-type", "application/json; charset=utf8")
                  .afterJSON(function(evComments) {
                    if(typeof evComments.comments == 'object') {
                      for(var i in evComments.comments) {
                        var comment = evComments.comments[i];
                        checkEventComment(comment);
                      }
                    }
					      }).toss();

                frisby.create('Talks at ' + evt.name)
                  .get(evt.talks_uri + '?resultsperpage=3')
                  .expectStatus(200)
                  .expectHeader("content-type", "application/json; charset=utf8")
                  .afterJSON(function(evTalks) {
                    if(typeof evTalks.talks == 'object') {
                      for(var i in evTalks.talks) {
                        var talk = evTalks.talks[i];
                        checkTalk(talk);
                      }
                    }
					      }).toss();

              }).toss();
          }
  		  }).toss();
    }
  })
.toss();

frisby.create('Non-existent event')
  .get(baseURL + '/v2.1/events/100100')
  .expectStatus(404)
  .expectHeader("content-type", "application/json; charset=utf8")
.toss();

frisby.create('Non-existent talk')
  .get(baseURL + '/v2.1/talks/100100100')
  .expectStatus(404)
  .expectHeader("content-type", "application/json; charset=utf8")
.toss();

frisby.create('Non-existent event comment')
  .get(baseURL + '/v2.1/event_comments/100100')
  .expectStatus(404)
  .expectHeader("content-type", "application/json; charset=utf8")
.toss();

frisby.create('Non-existent talk comment')
  .get(baseURL + "/v2.1/talk_comments/100100100")
  .expectStatus(404)
  .expectHeader("content-type", "application/json; charset=utf8")
  .expectJSON(["Comment not found"])
  .toss();

frisby.create('Non-existent user')
  .get(baseURL + "/v2.1/users/100100100")
  .expectStatus(404)
  .expectHeader("content-type", "application/json; charset=utf8")
  .expectJSON(["User not found"])
  .toss();


function checkDate(fieldValue) {
  dateVal = new Date(fieldValue);
  expect(getObjectClass(dateVal)).toBe('Date');
  return true;
}

/**
 * getObjectClass 
 * 
 * stolen from: http://blog.magnetiq.com/post/514962277/finding-out-class-names-of-javascript-objects
 */
function getObjectClass(obj) {
  if (obj && obj.constructor && obj.constructor.toString) {
    var arr = obj.constructor.toString().match(
      /function\s*(\w+)/);

    if (arr && arr.length == 2) {
      return arr[1];
    }
  }

  return undefined;
}

function checkEvent(ev) {
  if (ev.href != null) {
    expect(ev.href).toBeDefined();
    expect(typeof ev.href).toBe('string');
    if (ev.href != '') {
      //expect(ev.href).toMatch(/^http/);
    }
  }
  if (ev.icon != null) {
    expect(ev.icon).toBeDefined();
    expect(typeof ev.icon).toBe('string');
  }

 // Check required fields
  expect(ev.name).toBeDefined();
  expect(ev.start_date).toBeDefined();
  expect(ev.end_date).toBeDefined();
  expect(ev.description).toBeDefined();
  expect(ev.href).toBeDefined();
  expect(ev.icon).toBeDefined();
  expect(ev.attendee_count).toBeDefined();
  expect(ev.uri).toBeDefined();
  expect(ev.verbose_uri).toBeDefined();
  expect(ev.comments_uri).toBeDefined();
  expect(ev.talks_uri).toBeDefined();
  expect(ev.website_uri).toBeDefined();
  expect(typeof ev.name).toBe('string');
  checkDate(ev.start_date);
  checkDate(ev.end_date);
  expect(typeof ev.description).toBe('string');
  expect(typeof ev.attendee_count).toBe('number');
  expect(typeof ev.uri).toBe('string');
  expect(typeof ev.verbose_uri).toBe('string');
  expect(typeof ev.comments_uri).toBe('string');
  expect(typeof ev.talks_uri).toBe('string');
  expect(typeof ev.website_uri).toBe('string');
}

function checkVerboseEvent(evt) {
  expect(evt.name).toBeDefined();
  expect(evt.start_date).toBeDefined();
  expect(evt.end_date).toBeDefined();
  expect(evt.description).toBeDefined();
  expect(evt.href).toBeDefined();
  expect(evt.icon).toBeDefined();
  expect(evt.latitude).toBeDefined();
  expect(evt.longitude).toBeDefined();
  expect(evt.tz_continent).toBeDefined();
  expect(evt.tz_place).toBeDefined();
  expect(evt.location).toBeDefined();
  expect(evt.attendee_count).toBeDefined();
  expect(evt.comments_enabled).toBeDefined();
  expect(evt.event_comments_count).toBeDefined();
  expect(evt.cfp_start_date).toBeDefined();
  expect(evt.cfp_end_date).toBeDefined();
  expect(evt.cfp_url).toBeDefined();
  expect(evt.uri).toBeDefined();
  expect(evt.verbose_uri).toBeDefined();
  expect(evt.comments_uri).toBeDefined();
  expect(evt.talks_uri).toBeDefined();
  expect(evt.website_uri).toBeDefined();
  expect(evt.all_talk_comments_uri).toBeDefined();

  expect(typeof evt.name).toBe('string', "Event name");
  checkDate(evt.start_date);
  checkDate(evt.end_date);
  expect(typeof evt.description).toBe('string');
  if (evt.href != null) {
    expect(typeof evt.href).toBe('string', "Event href");
  }
  if (evt.icon != null) {
    expect(typeof evt.icon).toBe('string');
  }
  expect(typeof evt.latitude).toBe('number');
  expect(typeof evt.longitude).toBe('number');
  expect(typeof evt.tz_continent).toBe('string');
  expect(typeof evt.tz_place).toBe('string');
  expect(typeof evt.location).toBe('string');
  expect(typeof evt.attendee_count).toBe('number');
  expect(typeof evt.comments_enabled).toBe('number');
  expect(typeof evt.event_comments_count).toBe('number');
  if (evt.cfp_start_date != null) {
    checkDate(evt.cfp_start_date);
  }
  if (evt.cfp_end_date != null) {
    checkDate(evt.cfp_end_date);
  }
  if (evt.cfp_url != null) {
    expect(typeof evt.cfp_url).toBe('string');
  }
  expect(typeof evt.uri).toBe('string');
  expect(typeof evt.verbose_uri).toBe('string');
  expect(typeof evt.comments_uri).toBe('string');
  expect(typeof evt.talks_uri).toBe('string');
  expect(typeof evt.website_uri).toBe('string');
  expect(typeof evt.all_talk_comments_uri).toBe('string');
}

function checkEventComment(comment) {
  expect(comment.comment).toBeDefined();
  expect(typeof comment.comment).toBe('string');
  expect(comment.created_date).toBeDefined();
  checkDate(comment.created_date);
  expect(comment.user_display_name).toBeDefined();
  if(typeof comment.user_uri != 'undefined') {
      expect(typeof comment.user_uri).toBe('string');
  }
  expect(comment.comment_uri).toBeDefined();
  expect(typeof comment.comment_uri).toBe('string');
  expect(comment.verbose_comment_uri).toBeDefined();
  expect(typeof comment.verbose_comment_uri).toBe('string');
  expect(comment.event_uri).toBeDefined();
  expect(typeof comment.event_uri).toBe('string');
  expect(comment.event_comments_uri).toBeDefined();
  expect(typeof comment.event_comments_uri).toBe('string');
}

function checkTalk(talk) {
  expect(talk.talk_title).toBeDefined();
  expect(typeof talk.talk_title).toBe('string');
  expect(talk.talk_description).toBeDefined();
  expect(typeof talk.talk_description).toBe('string');
  expect(talk.uri).toBeDefined();
  expect(typeof talk.uri).toBe('string');
  expect(talk.verbose_uri).toBeDefined();
  expect(typeof talk.verbose_uri).toBe('string');
  expect(talk.comments_uri).toBeDefined();
  expect(typeof talk.comments_uri).toBe('string');
  expect(talk.event_uri).toBeDefined();
  expect(typeof talk.event_uri).toBe('string');
  expect(talk.website_uri).toBeDefined();
  expect(typeof talk.website_uri).toBe('string');
  expect(talk.verbose_comments_uri).toBeDefined();
  expect(typeof talk.verbose_comments_uri).toBe('string');
  if(typeof talk.slides_link != 'undefined' && talk.slides_link != null) {
    expect(typeof talk.slides_link).toBe('string');
  }
  expect(talk.language).toBeDefined();
  expect(typeof talk.language).toBe('string');
  expect(talk.start_date).toBeDefined();
  checkDate(talk.start_date);
  if(typeof talk.average_rating != 'undefined') {
    expect(typeof talk.average_rating).toBe('number');
  }
  expect(talk.comments_enabled).toBeDefined();
  expect(typeof talk.comments_enabled).toBe('number');
  expect(talk.comment_count).toBeDefined();
  expect(typeof talk.comment_count).toBe('number');
  expect(talk.type).toBeDefined();
  expect(typeof talk.type).toBe('string');
}

