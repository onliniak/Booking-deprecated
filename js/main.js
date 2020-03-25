/* global fetch */
/* global tui */
/* global MicroModal */
var animal;
var timestamp_Start;
var timestamp_End;

identification = window.crypto.getRandomValues(new Uint32Array(1))
let radio = document.querySelector('input[name="service"]:checked')
if (radio === null) {} else if (radio.checked === true) {
  radio.checked = false
}

urlParams = new URLSearchParams(window.location.search);
salon_ID = urlParams.get('salon');

document.addEventListener('DOMContentLoaded', function() {

  fetch("/wp-content/plugins/tui.calendar/php/openTime.php", {
      method: "POST",
      body: parseInt(salon_ID)
    })
    .then((res) => {
      return res.json();
    })
    .then((time) => {
      sessionStorage.setItem('minTime', time[0].time_Start)
      sessionStorage.setItem('maxTime', time[0].time_End)
    })
}, false);

document.getElementById("service").addEventListener('change', (event) => {
  fetch("/wp-content/plugins/tui.calendar/php/category.php", {
      method: "POST",
      body: event.target.value
    })
    .then((res) => {
      return res.text();
    })
    .then((data) => {
      document.getElementById("animal").innerHTML = data
    })
});

function pxtwo() {
  for (var i = 0; i < document.querySelectorAll(".tui-full-calendar-time-date-schedule-block").length; i++) {
    document.querySelectorAll(".tui-full-calendar-time-date-schedule-block")[i].style.height = parseFloat(document.querySelectorAll(".tui-full-calendar-time-date-schedule-block")[i].clientHeight) + 2 + 'px';
  }
}

document.getElementById("animal").addEventListener('change', (event) => {

  let Service_ID = sessionStorage.getItem('Service_ID')

  fetch("/wp-content/plugins/tui.calendar/php/interval.php", {
      method: "POST",
      body: JSON.stringify({
        "ID": Service_ID,
        "animal": event.target.value
      })
    })
    .then((res) => {
      return res.text();
    })
    .then((data) => {
      sessionStorage.setItem('interval', data)
      pxtwo()
      animal = event.target.value
      document.querySelector(".first_column").style.display = 'block'
    });
});

function save_to_database() {
  fetch("/wp-content/plugins/tui.calendar/php/save_to_database.php", {
      method: "POST",
      body: JSON.stringify({
        //"Session_ID": hash().toString().substr(4, 10),
        "Service_ID": sessionStorage.getItem('Service_ID'),
        "Salon_ID": parseInt(salon_ID),
        "Animal_name": animal,
        "timestamp_Start": timestamp_Start.toISOString().slice(0, 19).replace('T', ' '),
        "timestamp_End": timestamp_End.toISOString().slice(0, 19).replace('T', ' '),
        "it's.me": hash()
      })
    })
    .then((res) => {
      return res.text();
    })
    .then((data) => {})
}

let minTime = sessionStorage.getItem('minTime')
let maxTime = sessionStorage.getItem('maxTime')

var Calendar = tui.Calendar;
var calendar = new Calendar('#calendar', {
  usageStatistics: false,
  defaultView: 'week',
  taskView: false,
  week: {
    workweek: true, // show only 5 days except for weekend
    //hourStart: minTime,
    //hourEnd: maxTime
  }
});

calendar.on('beforeCreateSchedule', function(event) {

  let currentDate = new Date();
  let bookedDate = event.start._date
  let hour = event.start.getHours()
  let serviceID = document.querySelector('input[name="service"]:checked').value
  let service_name = document.getElementById(serviceID).innerText
  let details = document.getElementById("booking-details")
  let interval = sessionStorage.getItem('interval')

  if (bookedDate < currentDate || hour <= minTime || hour >= maxTime) {
    document.querySelector('.tui-full-calendar-time-guide-creation').style.display = "none"
  } else {
    event.end = new Date(event.start + interval * 60000)

    timestamp_Start = event.start._date
    timestamp_End = event.end
    human_time_start = timestamp_Start.toLocaleTimeString('pl-PL')
    human_time_end = timestamp_End.toLocaleTimeString('pl-PL')
    human_day = timestamp_Start.toLocaleDateString('pl-PL')

    document.querySelector(".tui-full-calendar-time-guide-creation").style.height = interval * 0.86 + "px"
    document.querySelector(".tui-full-calendar-time-guide-creation").innerText = human_time_start + "-" + human_time_end

    let blue = document.querySelector(".tui-full-calendar-time-guide-creation")
    let headBlue = event.guide._styleStart[0]
    let bodyBlue = blue.offsetHeight
    let bigBlue = headBlue + bodyBlue

    let green = document.querySelectorAll(".tui-full-calendar-time-date-schedule-block")

    var greenS = [];
    for (var i = 0; i < green.length; i++) {

      greenS.push({
        headGreen: green[i].offsetTop,
        bodyGreen: green[i].offsetHeight,
        bigGreen: green[i].offsetTop + green[i].offsetHeight,
        id: green[i].parentNode.parentNode.className.substr(37, 2)
      });
    }

    blocked = false
    greenS.forEach(function(entry) {
      let oversize = entry.headGreen - headBlue
      let daYid = blue.parentElement.className
      if (headBlue < entry.headGreen && oversize < bodyBlue && daYid.substr(37, 2) === entry.id) {
        document.querySelector('.tui-full-calendar-time-guide-creation').style.display = "none"
        blocked = true
      }
    });

    rezerwacja = new Object();
    rezerwacja.service = service_name
    rezerwacja.animal = animal
    rezerwacja.day = human_day
    rezerwacja.start = human_time_start
    rezerwacja.end = human_time_end

    //localStorage.setItem('booking', JSON.stringify(rezerwacja))
    //document.getElementById("booking_save").value = JSON.stringify(rezerwacja)

    details.innerHTML += "Usługa"
    details.innerHTML += "<br />"
    details.innerHTML += rezerwacja.service
    details.innerHTML += "<br />"
    details.innerHTML += "<br />"
    details.innerHTML += "Zwierzę"
    details.innerHTML += "<br />"
    details.innerHTML += rezerwacja.animal
    details.innerHTML += "<br />"
    details.innerHTML += "<br />"
    details.innerHTML += "Dzień"
    details.innerHTML += "<br />"
    details.innerHTML += rezerwacja.day
    details.innerHTML += "<br />"
    details.innerHTML += "<br />"
    details.innerHTML += "Godzina"
    details.innerHTML += "<br />"
    details.innerHTML += rezerwacja.start + "-" + rezerwacja.end
    details.innerHTML += "<br />"
    details.innerHTML += "<br />"
    details.innerHTML += "Przybliżony czas trwania"
    details.innerHTML += "<br />"
    details.innerHTML += interval + " minut"
    details.innerHTML += "<br />"
    details.innerHTML += "<br />"


    if (blocked === false) {
      MicroModal.show('booking');
      //save_to_database()
    }
  }
});

MicroModal.init({
  awaitCloseAnimation: true
})

var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    /* Toggle between adding and removing the "active" class,
     to highlight the button that controls the panel */
    this.classList.toggle("active");

    /* Toggle between hiding and showing the active panel */
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}

function ajax_login() {
  var email = document.getElementById("username").value;
  var pass = document.getElementById("Pass").value;
  var secu = document.getElementById("security").value;

  fetch("/wp-content/plugins/tui.calendar/php/login.php", {
      method: "POST",
      body: JSON.stringify({
        //"Session_ID": hash().toString().substr(4, 10),
        "username": email,
        "password": pass,
        "security": secu
      })
    })
    .then((res) => {
      return res.text();
    })
    .then((data) => {})
}

function ajax_register() {
  var user = document.getElementById("username_regi").value;
  var pass = document.getElementById("Pass_regi").value;
  var email = document.getElementById("email_regi").value;
  var secu = document.getElementById("security2").value;

  fetch("/wp-content/plugins/tui.calendar/php/register.php", {
      method: "POST",
      body: JSON.stringify({
        //"Session_ID": hash().toString().substr(4, 10),
        "username": user,
        "password": pass,
        "email": email,
        "security": secu
      })
    })
    .then((res) => {
      return res.text();
    })
    .then((data) => {})
}

function hash() {
  timestamp = new Date()
  // dzień tygodnia w liczbie
  day = timestamp.getDay()
  // timestamp
  today = timestamp.getTime()
  minute = today.toString().substr(7, 3)

  return minute.concat(day).concat(identification)
}