class Timer {
  _intervalId;
  _intervalSet;
  
  initialTime;
  timeRemaining;
  timerName;

  constructor(timeInMins, timerName) {
    this.timerName = timerName;
    this.initialTime = timeInMins * 60;
    this.timeRemaining = this.initialTime;
    this._intervalSet = false;
  }

  decrement() {
    if (this.timeRemaining > 0) this.timeRemaining--;
  }

  startTimer(runningSideEffects, endSideEffects) {
    if (this._intervalSet) return;

    this._intervalSet = true;

    // To start the timer exactly when pressed
    this.decrement();
    runningSideEffects();

    this._intervalId = setInterval(() => {
      if (this.timeRemaining == 0) {
        this.pauseTimer();
        endSideEffects();
      }

      this.decrement();
      runningSideEffects();
    }, 1000);
  }

  pauseTimer() {
    this._intervalSet = false;
    clearInterval(this._intervalId);
  }

  resetTimer() {
    this.pauseTimer();
    this.timeRemaining = this.initialTime;
  }

  getTimeRemaining() {
    let minutes = Math.floor(this.timeRemaining / 60);
    minutes = (minutes < 10 ? '0' : '') + minutes;

    let seconds = this.timeRemaining - (minutes * 60);
    seconds = (seconds < 10 ? '0' : '') + seconds;

    return (minutes + ":" + seconds);
  }

  getName() {
    return this.timerName;
  }
  
  timerRunning() {
    return this._intervalSet;
  }
}