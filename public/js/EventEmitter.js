/**
 * Class represents an event emitter
 */
export class EventEmitter {
  constructor() {
    this.listeners = {};
  }

  /**
   * On
   *
   * @param {*} eventName 
   * @param {*} listener 
   */
  on(eventName, listener) {
    if (!this.listeners[eventName]) this.listeners[eventName] = [];
    this.listeners[eventName].push(listener);
  }

  /**
   * Emit
   *
   * @param {*} eventName 
   * @param {*} data 
   */
  emit(eventName, data) {
    (this.listeners[eventName] || []).forEach(fn => fn(data));
  }
}