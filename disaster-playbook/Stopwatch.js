class Stopwatch {
  _intervalId;
  _intervalSet;
  
  stopwatchName;
  count;

  constructor(name) {
    this.stopwatchName = name;
    this.count = 0;
    this._intervalSet = false;
  }

  increment() {
    this.count++;
  }

  startStopwatch(startSideEffects, runningSideEffects) {
    if (this._intervalSet) return;

    this._intervalSet = true;

    startSideEffects();

    this._intervalId = setInterval(() => {
      this.increment();
      runningSideEffects();
    }, 1000);
  }

  stopStopwatch(stopSideEffects) {
    this._intervalSet = false;
    clearInterval(this._intervalId);
    stopSideEffects();
  }

  resetStopwatch(stopSideEffects) {
    this.stopStopwatch(stopSideEffects);
    this.count = 0;
  }

  getTimeCount() {
    let minutes = Math.floor(this.count / 60);
    minutes = (minutes < 10 ? '0' : '') + minutes;

    let seconds = this.count - (minutes * 60);
    seconds = (seconds < 10 ? '0' : '') + seconds;

    return (minutes + ":" + seconds);
  }
}