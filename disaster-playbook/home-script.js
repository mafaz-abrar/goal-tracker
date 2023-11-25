numberOfPomodoros = 1;


// FIGURE OUT HOW TO ADD INTERRUPTION COUNTERS
$(document).ready(() => {

  timerType = 'work'

  timer = switchTimer(timerType);
  updateView();

  stopwatch = new Stopwatch('interruption');

  // Arrow functions don't have 'this'
  $('#timer-button').on('click', function (e) {

    playButtonSound();

    if (!timer.timerRunning()) {
      timer.startTimer(timerRunningSideEffects, timerEndSideEffects);
      stopwatch.resetStopwatch(stopwatchStopSideEffects);
      $(this).html('PAUSE');

    }
    else {
      timer.pauseTimer();

      $(this).html('00:00');

      stopwatch.startStopwatch(stopwatchStartSideEffects, stopwatchRunningSideEffects);
    }
  })

  // DO THE 3D BUTTON EFFECT
  // $('#start-button').mousedown()

  $('.timer-type-button').on('click', function (e) {
    switch($(this).attr('id')) {
      case 'work-timer-button':
        timerType = 'work';
        break;
      case 'sbreak-timer-button':
        timerType = 'sbreak';
        break;
      case 'lbreak-timer-button':
        timerType = 'lbreak';
        break;
    }

    timer = switchTimer(timerType);
    $('#timer-button').html('START');
    updateView();
  })
})

// This will be hard to maintain...
// Violation of OOP principles here on out...
function timerRunningSideEffects() {
  updateView()
}

function timerEndSideEffects() {
  playAlarmSound();

  switch(timer.getName()) {
    case 'work':
      if (numberOfPomodoros % 4 == 0) {
        numberOfPomodoros = 1;
        timer = switchTimer('lbreak');
      } 
      else {
        timer = switchTimer('sbreak');
      } 

      break;
    case 'sbreak':
    case 'lbreak':
      numberOfPomodoros++;
      timer = switchTimer('work');
      break;
  }

  updateView();
  $('#timer-button').html('START');
}

function updateView() {
  $('#timer-text').html(timer.getTimeRemaining());
  $('title').html(timer.getTimeRemaining() + ' - Disaster Playbook');
  $('#pomodoro-number').html('#' + (numberOfPomodoros));
}

function playButtonSound() {
  let sound = new Audio('./res/sounds/button-sound.mp3');
  sound.loop = false;
  sound.play();
}

function playAlarmSound() {
  let sound = new Audio('./res/sounds/alarm-sound.mp3');
  sound.loop = false;
  sound.play();
}

// ONE FUNCTION TO CHANGE DETAILS FOR PAGE
function switchTimer(timerType) {

  switch (timerType) {
    case 'work':
      $('body').css('background', 'var(--red)');
      $('#timer-button').css('color', 'var(--red)');
      return new Timer(25, 'work');
      break;

    case 'sbreak':
      $('body').css('background', 'var(--blue)');
      $('#timer-button').css('color', 'var(--blue)');
      return new Timer(5, 'sbreak');
      break;

    case 'lbreak':
      $('body').css('background', 'var(--darkblue)');
      $('#timer-button').css('color', 'var(--darkblue)');
      return new Timer(15, 'lbreak');
      break;
  }
}

function stopwatchStartSideEffects() {

}

function stopwatchRunningSideEffects() {
  $('#timer-button').html(stopwatch.getTimeCount()); 
}

function stopwatchStopSideEffects() {

}