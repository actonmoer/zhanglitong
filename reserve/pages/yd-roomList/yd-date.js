var u = require('../../utils/util.js');

// const years = [], hours = [], minutes = [], seconds = [];
let days = [], years = [], hours = [], minutes = [], months = [], seconds = [];
let arrTime = [];

const checkDateMM = (YYYY,MM) => {
  months = [];
  let month = MM || u.dateM();
  let i = 1;
  if (YYYY == u.dateY()){
    i = month;
  }
  // console.log(i);
  for (; i <= 12; i++) {
    months.push(u.formatNumber(i) + '月');
  }
  return months;
}

const checkDateDD = (MM, DD) => {
  days = [];
  let sday = DD || u.dateD(), eday = '', i = 1;
  if (MM == '1' || MM == '3' || MM == '5' || MM == '7' || MM == '8' || MM == '10' || MM == '12') {
    eday = 31;
  } else if (MM == '4' || MM == '6' || MM == '9' || MM == '11') {
    eday = 30;
  } else if (MM == '2') {
    eday = u.dateY() % 4 == 0 && u.dateY() % 100 != 0 || u.dateY() % 400 == 0 ? 29 : 28;
  }
  if (MM == u.dateM()){
    i = sday;
  }
  for (; i <= eday; i++) {
    days.push(u.formatNumber(i) + '日');
  }
  return days;
}

const checkDateHH = (DD,HH) => {
  hours = [];
  let house = HH || u.dateH(), i = 0;

  if (DD == u.dateD()){
    i = house;
  }

  for(;i<24;i++){
    hours.push(i+'时');
  }
  return hours;
}

const checkDatemms = (HH, mm) => {
  minutes = [];
  let mms = mm || u.datems(), i = 0;

  if (HH == u.dateH()) {
    i = mms;
  }

  for (; i < 60; i++) {
    minutes.push(u.formatNumber(i) + '分');
  }
  return minutes;
}

const checkDays = M => {
  let eday = ''; 
  if (M == '1' || M == '3' || M == '5' || M == '7' || M == '8' || M == '10' || M == '12') {
    eday = 31;
  } else if (M == '4' || M == '6' || M == '9' || M == '11') {
    eday = 30;
  } else if (M == '2') {
    eday = u.dateY() % 4 == 0 && u.dateY() % 100 != 0 || u.dateY() % 400 == 0 ? 29 : 28;
  }
  return eday;
}

years.push(u.dateY() + '年');
years.push((parseInt(u.dateY()) + 1) + '年');
for (let _MM = u.dateM(); _MM <= 12; _MM++) {
  months.push(_MM + '月');
}
for (let _DD = u.dateD(); _DD <= checkDateDD(u.dateM()); _DD++) {
  days.push(_DD + '日');
}
for (let _HH = u.dateH(); _HH <= 23; _HH++) {
  hours.push(_HH + '时');
}
for (let _mms = u.datems(); _mms <= 59; _mms++) {
  minutes.push(_mms + '分');
}

arrTime[0] = years;
arrTime[1] = months;
arrTime[2] = days;
arrTime[3] = hours;
arrTime[4] = minutes;

const endCheckYYYY = (y) => {
  arrTime[1] = [];
  arrTime[2] = [];

  months = [], days = [], minutes = [], hours = [];

  if (y == u.dateY()){


    for (let _M = u.dateM(); _M <= 12; _M++) {
      months.push(u.formatNumber(_M) + '月');
    }
    for (let _D = u.dateD(); _D <= checkDays(u.dateM()); _D++) {
      days.push(u.formatNumber(_D) + '日');
    }
    for (let _H = u.dateH(); _H <= 23; _H++) {
      hours.push(u.formatNumber(_H) + '时');
    }
    for (let _mm = u.datems(); _mm <= 59; _mm++) {
      minutes.push(u.formatNumber(_mm) + '分');
    }
  } else {


    for (let _M = 1; _M <= 12; _M++) {
      months.push(u.formatNumber(_M) + '月');
    }
    for (let _D = 1; _D <= checkDays(1); _D++) {
      days.push(u.formatNumber(_D) + '日');
    }
    for (let _H = 0; _H <= 23; _H++) {
      hours.push(u.formatNumber(_H) + '时');
    }
    for (let _mm = 0; _mm <= 59; _mm++) {
      minutes.push(u.formatNumber(_mm) + '分');
    }
  }
  arrTime[1] = months;
  arrTime[2] = days;
  arrTime[3] = hours;
  arrTime[4] = minutes;
  console.log(arrTime);
  return arrTime;
}

const endCheckMM = (m,y) => {
  days = [], hours = [], minutes = [];
  if(y == u.dateY()){


    if(m == u.dateM()){


      for (let _D = u.dateD(); _D <= checkDays(m); _D++) {
        days.push(u.formatNumber(_D) + '日');
      }
      for (let _H = u.dateH(); _H <= 23; _H++) {
        hours.push(u.formatNumber(_H) + '时');
      }
      for (let _mm = u.datems(); _mm <= 59; _mm++) {
        minutes.push(u.formatNumber(_mm) + '分');
      }
    }else{


      for (let _D = 1; _D <= checkDays(m); _D++) {
        days.push(u.formatNumber(_D) + '日');
      }
      for (let _H = 0; _H <= 23; _H++) {
        hours.push(u.formatNumber(_H) + '时');
      }
      for (let _mm = 0; _mm <= 59; _mm++) {
        minutes.push(u.formatNumber(_mm) + '分');
      }
    }
  }else{


    for (let _D = 1; _D <= checkDays(m); _D++) {
      days.push(u.formatNumber(_D) + '日');
    }
    for (let _H = 0; _H <= 23; _H++) {
      hours.push(u.formatNumber(_H) + '时');
    }
    for (let _mm = 0; _mm <= 59; _mm++) {
      minutes.push(u.formatNumber(_mm) + '分');
    }
  }
  arrTime[1] = months;
  arrTime[2] = days;
  arrTime[3] = hours;
  arrTime[4] = minutes;
  // console.log(arrTime);
  return arrTime;
}

const endCheckDD = (d,m,y) => {
  minutes = [], hours = [];
  if(y == u.dateY()){


    if(m == u.dateM()){


      if(d == u.dateD()){


        for (let _H = u.dateH(); _H <= 23; _H++) {
          hours.push(u.formatNumber(_H) + '时');
        }
        for (let _mm = u.datems(); _mm <= 59; _mm++) {
          minutes.push(u.formatNumber(_mm) + '分');
        }
      }else{


        for (let _H = 0; _H <= 23; _H++) {
          hours.push(u.formatNumber(_H) + '时');
        }
        for (let _mm = 0; _mm <= 59; _mm++) {
          minutes.push(u.formatNumber(_mm) + '分');
        }
      }
    }else{


      for (let _H = 0; _H <= 23; _H++) {
        hours.push(u.formatNumber(_H) + '时');
      }
      for (let _mm = 0; _mm <= 59; _mm++) {
        minutes.push(u.formatNumber(_mm) + '分');
      }
    }
  }else{
    for (let _H = 0; _H <= 23; _H++) {
      hours.push(u.formatNumber(_H) + '时');
    }
    for (let _mm = 0; _mm <= 59; _mm++) {
      minutes.push(u.formatNumber(_mm) + '分');
    }
  }
  arrTime[1] = months;
  arrTime[2] = days;
  arrTime[3] = hours;
  arrTime[4] = minutes;
  return arrTime;
}

const endCheckHH = (h,d,m,y) => {
  minutes = [];
  if(y == u.dateY()){
    if(m == u.dateM()){
      if(d == u.dateD()){
        if(h == u.dateH()){
          for (let _mm = u.datems(); _mm <= 59; _mm++) {
            minutes.push(u.formatNumber(_mm) + '分');
          }
        }else{
          for (let _mm = 0; _mm <= 59; _mm++) {
            minutes.push(u.formatNumber(_mm) + '分');
          }
        }
      }else{
        for (let _mm = 0; _mm <= 59; _mm++) {
          minutes.push(u.formatNumber(_mm) + '分');
        }
      }
    }else{
      for (let _mm = 0; _mm <= 59; _mm++) {
        minutes.push(u.formatNumber(_mm) + '分');
      }
    }
  }else{
    for (let _mm = 0; _mm <= 59; _mm++) {
      minutes.push(u.formatNumber(_mm) + '分');
    }
  }
  arrTime[1] = months;
  arrTime[2] = days;
  arrTime[3] = hours;
  arrTime[4] = minutes;
  // console.log(arrTime);
  return arrTime;
}

const endCheckmms = (mm, h, d, m, y) => {
  minutes = [];
  if (y == u.dateY()) {
    if (m == u.dateM()) {
      if (d == u.dateD()) {
        if (h == u.dateH()) {
          if (mm >= u.datems()){
            for (let _mm = u.datems(); _mm <= 59; _mm++) {
              minutes.push(u.formatNumber(_mm) + '分');
            }
          }
        } else {
          for (let _mm = 0; _mm <= 59; _mm++) {
            minutes.push(u.formatNumber(_mm) + '分');
          }
        }
      } else {
        for (let _mm = 0; _mm <= 59; _mm++) {
          minutes.push(u.formatNumber(_mm) + '分');
        }
      }
    } else {
      for (let _mm = 0; _mm <= 59; _mm++) {
        minutes.push(u.formatNumber(_mm) + '分');
      }
    }
  } else {
    for (let _mm = 0; _mm <= 59; _mm++) {
      minutes.push(u.formatNumber(_mm) + '分');
    }
  }
  arrTime[1] = months;
  arrTime[2] = days;
  arrTime[3] = hours;
  arrTime[4] = minutes;
  // console.log(arrTime);
  return arrTime;
}
module.exports = {
  year: years,
  month: months,
  day: days,
  hour: hours,
  minute: minutes,
  second: seconds,
  checkMM: checkDateMM,
  checkDD: checkDateDD,
  checkHH: checkDateHH,
  checkmms: checkDatemms,
  endCheckYYYY: endCheckYYYY,
  endCheckMM: endCheckMM,
  endCheckDD: endCheckDD,
  endCheckHH: endCheckHH,
  endCheckmms: endCheckmms,
  arrTime: arrTime
}