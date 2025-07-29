/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/blocks/advanced-video/front.js":
/*!********************************************!*\
  !*** ./src/blocks/advanced-video/front.js ***!
  \********************************************/
/***/ (() => {

function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return e; }; var t, e = {}, r = Object.prototype, n = r.hasOwnProperty, o = Object.defineProperty || function (t, e, r) { t[e] = r.value; }, i = "function" == typeof Symbol ? Symbol : {}, a = i.iterator || "@@iterator", c = i.asyncIterator || "@@asyncIterator", u = i.toStringTag || "@@toStringTag"; function define(t, e, r) { return Object.defineProperty(t, e, { value: r, enumerable: !0, configurable: !0, writable: !0 }), t[e]; } try { define({}, ""); } catch (t) { define = function define(t, e, r) { return t[e] = r; }; } function wrap(t, e, r, n) { var i = e && e.prototype instanceof Generator ? e : Generator, a = Object.create(i.prototype), c = new Context(n || []); return o(a, "_invoke", { value: makeInvokeMethod(t, r, c) }), a; } function tryCatch(t, e, r) { try { return { type: "normal", arg: t.call(e, r) }; } catch (t) { return { type: "throw", arg: t }; } } e.wrap = wrap; var h = "suspendedStart", l = "suspendedYield", f = "executing", s = "completed", y = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var p = {}; define(p, a, function () { return this; }); var d = Object.getPrototypeOf, v = d && d(d(values([]))); v && v !== r && n.call(v, a) && (p = v); var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p); function defineIteratorMethods(t) { ["next", "throw", "return"].forEach(function (e) { define(t, e, function (t) { return this._invoke(e, t); }); }); } function AsyncIterator(t, e) { function invoke(r, o, i, a) { var c = tryCatch(t[r], t, o); if ("throw" !== c.type) { var u = c.arg, h = u.value; return h && "object" == _typeof(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) { invoke("next", t, i, a); }, function (t) { invoke("throw", t, i, a); }) : e.resolve(h).then(function (t) { u.value = t, i(u); }, function (t) { return invoke("throw", t, i, a); }); } a(c.arg); } var r; o(this, "_invoke", { value: function value(t, n) { function callInvokeWithMethodAndArg() { return new e(function (e, r) { invoke(t, n, e, r); }); } return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(e, r, n) { var o = h; return function (i, a) { if (o === f) throw Error("Generator is already running"); if (o === s) { if ("throw" === i) throw a; return { value: t, done: !0 }; } for (n.method = i, n.arg = a;;) { var c = n.delegate; if (c) { var u = maybeInvokeDelegate(c, n); if (u) { if (u === y) continue; return u; } } if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) { if (o === h) throw o = s, n.arg; n.dispatchException(n.arg); } else "return" === n.method && n.abrupt("return", n.arg); o = f; var p = tryCatch(e, r, n); if ("normal" === p.type) { if (o = n.done ? s : l, p.arg === y) continue; return { value: p.arg, done: n.done }; } "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg); } }; } function maybeInvokeDelegate(e, r) { var n = r.method, o = e.iterator[n]; if (o === t) return r.delegate = null, "throw" === n && e.iterator["return"] && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y; var i = tryCatch(o, e.iterator, r.arg); if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y; var a = i.arg; return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y); } function pushTryEntry(t) { var e = { tryLoc: t[0] }; 1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e); } function resetTryEntry(t) { var e = t.completion || {}; e.type = "normal", delete e.arg, t.completion = e; } function Context(t) { this.tryEntries = [{ tryLoc: "root" }], t.forEach(pushTryEntry, this), this.reset(!0); } function values(e) { if (e || "" === e) { var r = e[a]; if (r) return r.call(e); if ("function" == typeof e.next) return e; if (!isNaN(e.length)) { var o = -1, i = function next() { for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next; return next.value = t, next.done = !0, next; }; return i.next = i; } } throw new TypeError(_typeof(e) + " is not iterable"); } return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), o(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) { var e = "function" == typeof t && t.constructor; return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name)); }, e.mark = function (t) { return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t; }, e.awrap = function (t) { return { __await: t }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () { return this; }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) { void 0 === i && (i = Promise); var a = new AsyncIterator(wrap(t, r, n, o), i); return e.isGeneratorFunction(r) ? a : a.next().then(function (t) { return t.done ? t.value : a.next(); }); }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () { return this; }), define(g, "toString", function () { return "[object Generator]"; }), e.keys = function (t) { var e = Object(t), r = []; for (var n in e) r.push(n); return r.reverse(), function next() { for (; r.length;) { var t = r.pop(); if (t in e) return next.value = t, next.done = !1, next; } return next.done = !0, next; }; }, e.values = values, Context.prototype = { constructor: Context, reset: function reset(e) { if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t); }, stop: function stop() { this.done = !0; var t = this.tryEntries[0].completion; if ("throw" === t.type) throw t.arg; return this.rval; }, dispatchException: function dispatchException(e) { if (this.done) throw e; var r = this; function handle(n, o) { return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o; } for (var o = this.tryEntries.length - 1; o >= 0; --o) { var i = this.tryEntries[o], a = i.completion; if ("root" === i.tryLoc) return handle("end"); if (i.tryLoc <= this.prev) { var c = n.call(i, "catchLoc"), u = n.call(i, "finallyLoc"); if (c && u) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } else if (c) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); } else { if (!u) throw Error("try statement without catch or finally"); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } } } }, abrupt: function abrupt(t, e) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var o = this.tryEntries[r]; if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) { var i = o; break; } } i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null); var a = i ? i.completion : {}; return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a); }, complete: function complete(t, e) { if ("throw" === t.type) throw t.arg; return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y; }, finish: function finish(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y; } }, "catch": function _catch(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.tryLoc === t) { var n = r.completion; if ("throw" === n.type) { var o = n.arg; resetTryEntry(r); } return o; } } throw Error("illegal catch attempt"); }, delegateYield: function delegateYield(e, r, n) { return this.delegate = { iterator: values(e), resultName: r, nextLoc: n }, "next" === this.method && (this.arg = t), y; } }, e; }
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
(function (e) {
  var t = {};
  function r(n) {
    if (t[n]) return t[n].exports;
    var a = t[n] = {
      i: n,
      l: false,
      exports: {}
    };
    e[n].call(a.exports, a, a.exports, r);
    a.l = true;
    return a.exports;
  }
  r.m = e;
  r.c = t;
  r.d = function (e, t, n) {
    if (!r.o(e, t)) Object.defineProperty(e, t, {
      enumerable: true,
      get: n
    });
  };
  r.r = function (e) {
    if ("undefined" !== typeof Symbol && Symbol.toStringTag) Object.defineProperty(e, Symbol.toStringTag, {
      value: "Module"
    });
    Object.defineProperty(e, "__esModule", {
      value: true
    });
  };
  r.t = function (e, t) {
    if (1 & t) e = r(e);
    if (8 & t) return e;
    if (4 & t && "object" === _typeof(e) && e && e.__esModule) return e;
    var n = Object.create(null);
    r.r(n);
    Object.defineProperty(n, "default", {
      enumerable: true,
      value: e
    });
    if (2 & t && "string" != typeof e) for (var a in e) r.d(n, a, function (t) {
      return e[t];
    }.bind(null, a));
    return n;
  };
  r.n = function (e) {
    var t = e && e.__esModule ? function t() {
      return e["default"];
    } : function t() {
      return e;
    };
    r.d(t, "a", t);
    return t;
  };
  r.o = function (e, t) {
    return Object.prototype.hasOwnProperty.call(e, t);
  };
  r.p = "/";
  return r(r.s = 0);
})({
  0: function _(e, t, r) {
    r("3fe06cb9615786e0ea67");
    e.exports = r("3177845424933048caec");
  },
  "0116c75136b233002fb9": function c75136b233002fb9(e, t) {
    var r = {}.toString;
    e.exports = function (e) {
      return r.call(e).slice(8, -1);
    };
  },
  "03192b087bb464f059c0": function b087bb464f059c0(e, t, r) {
    "use strict";

    var n = r("d394d0a4c1838202489d");
    var a = r("23d432718b3e8ce32362");
    var c = r("ca19e32ab6ee3b3692aa");
    e.exports = function e(t) {
      var r = n(this);
      var o = c(r.length);
      var i = arguments.length;
      var f = a(i > 1 ? arguments[1] : void 0, o);
      var u = i > 2 ? arguments[2] : void 0;
      var d = void 0 === u ? o : a(u, o);
      while (d > f) r[f++] = t;
      return r;
    };
  },
  "0565be87d109f51269ca": function be87d109f51269ca(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("ecd0e0fb36455021f082")(2);
    n(n.P + n.F * !r("40b064d4eb439c0c1c2b")([].filter, true), "Array", {
      filter: function e(t) {
        return a(this, t, arguments[1]);
      }
    });
  },
  "0594430ce9777a0767aa": function ce9777a0767aa(e, t, r) {
    var n = r("6ae9955278ddcf01bbfd");
    e.exports = function (e, t) {
      return new (n(e))(t);
    };
  },
  "06b2b473e53cc019ca74": function b2b473e53cc019ca74(e, t, r) {
    "use strict";

    var n = r("d394d0a4c1838202489d");
    var a = r("23d432718b3e8ce32362");
    var c = r("ca19e32ab6ee3b3692aa");
    e.exports = [].copyWithin || function e(t, r) {
      var o = n(this);
      var i = c(o.length);
      var f = a(t, i);
      var u = a(r, i);
      var d = arguments.length > 2 ? arguments[2] : void 0;
      var s = Math.min((void 0 === d ? i : a(d, i)) - u, i - f);
      var v = 1;
      if (u < f && f < u + s) {
        v = -1;
        u += s - 1;
        f += s - 1;
      }
      while (s-- > 0) {
        if (u in o) o[f] = o[u];else delete o[f];
        f += v;
        u += v;
      }
      return o;
    };
  },
  "0a5193b0534d54dbcda1": function a5193b0534d54dbcda1(e, t, r) {
    "use strict";

    var n = r("0e717e1f47872428608d");
    var a = r("2d13d93c336705cd8ff9");
    var c = r("d394d0a4c1838202489d");
    var o = r("4357234160788ed77205");
    var i = r("71d3ec5bd65418e28ebc");
    var f = r("ca19e32ab6ee3b3692aa");
    var u = r("8de492c765fbfb624515");
    var d = r("f88518adc3004bf8d923");
    a(a.S + a.F * !r("574ea81f94fc0b2f332a")(function (e) {
      Array.from(e);
    }), "Array", {
      from: function e(t) {
        var r = c(t);
        var a = "function" == typeof this ? this : Array;
        var s = arguments.length;
        var v = s > 1 ? arguments[1] : void 0;
        var l = void 0 !== v;
        var b = 0;
        var h = d(r);
        var p, y, m, g;
        if (l) v = n(v, s > 2 ? arguments[2] : void 0, 2);
        if (void 0 != h && !(a == Array && i(h))) for (g = h.call(r), y = new a(); !(m = g.next()).done; b++) u(y, b, l ? o(g, v, [m.value, b], true) : m.value);else {
          p = f(r.length);
          for (y = new a(p); p > b; b++) u(y, b, l ? v(r[b], b) : r[b]);
        }
        y.length = b;
        return y;
      }
    });
  },
  "0e717e1f47872428608d": function e717e1f47872428608d(e, t, r) {
    var n = r("4d073e2fededcdd3f3d3");
    e.exports = function (e, t, r) {
      n(e);
      if (void 0 === t) return e;
      switch (r) {
        case 1:
          return function (r) {
            return e.call(t, r);
          };
        case 2:
          return function (r, n) {
            return e.call(t, r, n);
          };
        case 3:
          return function (r, n, a) {
            return e.call(t, r, n, a);
          };
      }
      return function () {
        return e.apply(t, arguments);
      };
    };
  },
  "0f025c284bc567debf3d": function f025c284bc567debf3d(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("ecd0e0fb36455021f082")(0);
    var c = r("40b064d4eb439c0c1c2b")([].forEach, true);
    n(n.P + n.F * !c, "Array", {
      forEach: function e(t) {
        return a(this, t, arguments[1]);
      }
    });
  },
  "1139de1233fd67f45bd7": function de1233fd67f45bd7(e, t) {
    var r = e.exports = "undefined" != typeof window && window.Math == Math ? window : "undefined" != typeof self && self.Math == Math ? self : Function("return this")();
    if ("number" == typeof __g) __g = r;
  },
  "12323e1413cbffdabe02": function e1413cbffdabe02(e, t, r) {
    var n = r("d34390b928ecadc11dc8");
    var a = r("d394d0a4c1838202489d");
    var c = r("5f6eded14a8ed86276fc")("IE_PROTO");
    var o = Object.prototype;
    e.exports = Object.getPrototypeOf || function (e) {
      e = a(e);
      if (n(e, c)) return e[c];
      if ("function" == typeof e.constructor && e instanceof e.constructor) return e.constructor.prototype;
      return e instanceof Object ? o : null;
    };
  },
  "145c4f2bdc487b2578df": function c4f2bdc487b2578df(e, t, r) {
    var n = r("e3136eea77d23f8d97ec");
    var a = r("380c0734c088ca78ccda");
    e.exports = Object.keys || function e(t) {
      return n(t, a);
    };
  },
  "1468f5d37f30f63d76ea": function f5d37f30f63d76ea(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("efae14a3f8cd9a616256");
    var c = r("1f3157d073736826d4fa");
    var o = r("ca19e32ab6ee3b3692aa");
    var i = [].lastIndexOf;
    var f = !!i && 1 / [1].lastIndexOf(1, -0) < 0;
    n(n.P + n.F * (f || !r("40b064d4eb439c0c1c2b")(i)), "Array", {
      lastIndexOf: function e(t) {
        if (f) return i.apply(this, arguments) || 0;
        var r = a(this);
        var n = o(r.length);
        var u = n - 1;
        if (arguments.length > 1) u = Math.min(u, c(arguments[1]));
        if (u < 0) u = n + u;
        for (; u >= 0; u--) if (u in r) if (r[u] === t) return u || 0;
        return -1;
      }
    });
  },
  "1591b90b4327c94245ad": function b90b4327c94245ad(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("ecd0e0fb36455021f082")(1);
    n(n.P + n.F * !r("40b064d4eb439c0c1c2b")([].map, true), "Array", {
      map: function e(t) {
        return a(this, t, arguments[1]);
      }
    });
  },
  "1b01d0e59d3af79760a0": function b01d0e59d3af79760a0(e, t, r) {
    var n = r("79c91dcd8cd554c59c7b");
    var a = r("bc1a333d2b6eceac22e3");
    var c = r("4ef7a75a1d1957a7c5c0");
    var o = Object.defineProperty;
    t.f = r("5975c7ea3ecd56e534e3") ? Object.defineProperty : function e(t, r, i) {
      n(t);
      r = c(r, true);
      n(i);
      if (a) try {
        return o(t, r, i);
      } catch (f) {}
      if ("get" in i || "set" in i) throw TypeError("Accessors not supported!");
      if ("value" in i) t[r] = i.value;
      return t;
    };
  },
  "1b24b031f63a74d1f57f": function b24b031f63a74d1f57f(e, t, r) {
    "use strict";

    var n = r("f8f08bdd917755ac017a");
    function a() {}
    var c = null;
    var o = {};
    function i(e) {
      try {
        return e.then;
      } catch (t) {
        c = t;
        return o;
      }
    }
    function f(e, t) {
      try {
        return e(t);
      } catch (r) {
        c = r;
        return o;
      }
    }
    function u(e, t, r) {
      try {
        e(t, r);
      } catch (n) {
        c = n;
        return o;
      }
    }
    e.exports = d;
    function d(e) {
      if ("object" !== _typeof(this)) throw new TypeError("Promises must be constructed via new");
      if ("function" !== typeof e) throw new TypeError("Promise constructor's argument is not a function");
      this._U = 0;
      this._V = 0;
      this._W = null;
      this._X = null;
      if (e === a) return;
      m(e, this);
    }
    d._Y = null;
    d._Z = null;
    d._0 = a;
    d.prototype.then = function (e, t) {
      if (this.constructor !== d) return s(this, e, t);
      var r = new d(a);
      v(this, new y(e, t, r));
      return r;
    };
    function s(e, t, r) {
      return new e.constructor(function (n, c) {
        var o = new d(a);
        o.then(n, c);
        v(e, new y(t, r, o));
      });
    }
    function v(e, t) {
      while (3 === e._V) e = e._W;
      if (d._Y) d._Y(e);
      if (0 === e._V) {
        if (0 === e._U) {
          e._U = 1;
          e._X = t;
          return;
        }
        if (1 === e._U) {
          e._U = 2;
          e._X = [e._X, t];
          return;
        }
        e._X.push(t);
        return;
      }
      l(e, t);
    }
    function l(e, t) {
      n(function () {
        var r = 1 === e._V ? t.onFulfilled : t.onRejected;
        if (null === r) {
          if (1 === e._V) b(t.promise, e._W);else h(t.promise, e._W);
          return;
        }
        var n = f(r, e._W);
        if (n === o) h(t.promise, c);else b(t.promise, n);
      });
    }
    function b(e, t) {
      if (t === e) return h(e, new TypeError("A promise cannot be resolved with itself."));
      if (t && ("object" === _typeof(t) || "function" === typeof t)) {
        var r = i(t);
        if (r === o) return h(e, c);
        if (r === e.then && t instanceof d) {
          e._V = 3;
          e._W = t;
          p(e);
          return;
        } else if ("function" === typeof r) {
          m(r.bind(t), e);
          return;
        }
      }
      e._V = 1;
      e._W = t;
      p(e);
    }
    function h(e, t) {
      e._V = 2;
      e._W = t;
      if (d._Z) d._Z(e, t);
      p(e);
    }
    function p(e) {
      if (1 === e._U) {
        v(e, e._X);
        e._X = null;
      }
      if (2 === e._U) {
        for (var t = 0; t < e._X.length; t++) v(e, e._X[t]);
        e._X = null;
      }
    }
    function y(e, t, r) {
      this.onFulfilled = "function" === typeof e ? e : null;
      this.onRejected = "function" === typeof t ? t : null;
      this.promise = r;
    }
    function m(e, t) {
      var r = false;
      var n = u(e, function (e) {
        if (r) return;
        r = true;
        b(t, e);
      }, function (e) {
        if (r) return;
        r = true;
        h(t, e);
      });
      if (!r && n === o) {
        r = true;
        h(t, c);
      }
    }
  },
  "1b71f5e96f29a92413b6": function b71f5e96f29a92413b6(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("5b9dc0e027f5c68eff1a");
    var c = r("0116c75136b233002fb9");
    var o = r("23d432718b3e8ce32362");
    var i = r("ca19e32ab6ee3b3692aa");
    var f = [].slice;
    n(n.P + n.F * r("7a7739dd8a198a2cfcb5")(function () {
      if (a) f.call(a);
    }), "Array", {
      slice: function e(t, r) {
        var n = i(this.length);
        var a = c(this);
        r = void 0 === r ? n : r;
        if ("Array" == a) return f.call(this, t, r);
        var u = o(t, n);
        var d = o(r, n);
        var s = i(d - u);
        var v = new Array(s);
        var l = 0;
        for (; l < s; l++) v[l] = "String" == a ? this.charAt(u + l) : this[u + l];
        return v;
      }
    });
  },
  "1e0f00d296712713afe6": function e0f00d296712713afe6(e, t, r) {
    var n = r("4d073e2fededcdd3f3d3");
    var a = r("d394d0a4c1838202489d");
    var c = r("a537d0accb907bf9d41f");
    var o = r("ca19e32ab6ee3b3692aa");
    e.exports = function (e, t, r, i, f) {
      n(t);
      var u = a(e);
      var d = c(u);
      var s = o(u.length);
      var v = f ? s - 1 : 0;
      var l = f ? -1 : 1;
      if (r < 2) for (;;) {
        if (v in d) {
          i = d[v];
          v += l;
          break;
        }
        v += l;
        if (f ? v < 0 : s <= v) throw TypeError("Reduce of empty array with no initial value");
      }
      for (; f ? v >= 0 : s > v; v += l) if (v in d) i = t(i, d[v], v, u);
      return i;
    };
  },
  "1f3157d073736826d4fa": function f3157d073736826d4fa(e, t) {
    var r = Math.ceil;
    var n = Math.floor;
    e.exports = function (e) {
      return isNaN(e = +e) ? 0 : (e > 0 ? n : r)(e);
    };
  },
  "2076e982c14c41925fb4": function e982c14c41925fb4(e, t, r) {
    var n = r("efae14a3f8cd9a616256");
    var a = r("ca19e32ab6ee3b3692aa");
    var c = r("23d432718b3e8ce32362");
    e.exports = function (e) {
      return function (t, r, o) {
        var i = n(t);
        var f = a(i.length);
        var u = c(o, f);
        var d;
        if (e && r != r) while (f > u) {
          d = i[u++];
          if (d != d) return true;
        } else for (; f > u; u++) if (e || u in i) if (i[u] === r) return e || u || 0;
        return !e && -1;
      };
    };
  },
  "224ae09e536c09e69a0a": function ae09e536c09e69a0a(e, t) {
    e.exports = {};
  },
  "23617503340803072f22": function f22(e, t, r) {
    "use strict";

    r.d(t, "b", function () {
      return o;
    });
    r.d(t, "d", function () {
      return i;
    });
    r.d(t, "a", function () {
      return v;
    });
    r.d(t, "c", function () {
      return l;
    });
    function n(e, t, r, n, a, c, o) {
      try {
        var i = e[c](o);
        var f = i.value;
      } catch (u) {
        r(u);
        return;
      }
      if (i.done) t(f);else Promise.resolve(f).then(n, a);
    }
    function a(e) {
      return function () {
        var t = this,
          r = arguments;
        return new Promise(function (a, c) {
          var o = e.apply(t, r);
          function i(e) {
            n(o, a, c, i, f, "next", e);
          }
          function f(e) {
            n(o, a, c, i, f, "throw", e);
          }
          i(void 0);
        });
      };
    }
    function c(e) {
      "@babel/helpers - typeof";

      return c = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e;
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e;
      }, c(e);
    }
    function o() {
      return Math.round(1e17 * Math.random());
    }
    function i() {
      var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : [];
      var t = arguments.length > 1 ? arguments[1] : void 0;
      var r = arguments.length > 2 ? arguments[2] : void 0;
      if (!e.length || !t) return [];
      return e.reduce(function () {
        var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : [];
        var n = arguments.length > 1 ? arguments[1] : void 0;
        var a = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : 0;
        var c = Math.floor(a / t);
        if (!e[c]) e[c] = [];
        if (r) e[c].push(n[r]);else e[c].push(n);
        return e;
      }, []);
    }
    function f(e, t) {
      var r = t.type || "GET";
      var n = t.data;
      var a = t.success,
        o = void 0 === a ? function e(t, r) {
          void 0;
        } : a,
        i = t.fail,
        f = void 0 === i ? function e(t, r) {
          void 0;
        } : i;
      var u = new XMLHttpRequest();
      u.onreadystatechange = function () {
        if (4 === Number(u.readyState)) if (u.status >= 200 && u.status < 300 || 304 === u.status) o(u.responseText, u);else f(u.responseText, u);
      };
      function d(e) {
        var t = e;
        var r = [];
        if ("string" === typeof t) t = encodeURI(t);else if ("object" === c(t)) {
          Object.keys(t).forEach(function (e) {
            r.push("".concat(e, "=").concat(encodeURIComponent(t[e].toString())));
          });
          t = r.join("&");
        }
        return t;
      }
      if ("GET" === r.toUpperCase()) {
        var s = "".concat(e, "?").concat(d(n || ""));
        u.open("get", s, true);
        u.send(null);
      } else if ("POST" === r.toUpperCase()) {
        u.open("post", e, true);
        u.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        u.send(d(n || ""));
      }
    }
    function u(e, t) {
      return d.apply(this, arguments);
    }
    function d() {
      d = a(_regeneratorRuntime().mark(function e(t, r) {
        return _regeneratorRuntime().wrap(function e(n) {
          while (1) switch (n.prev = n.next) {
            case 0:
              return n.abrupt("return", new Promise(function (e, n) {
                var a = {
                  data: r,
                  success: e,
                  fail: n
                };
                f(t, a);
              }).then(function () {
                var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "{}";
                var t = {};
                try {
                  t = JSON.parse(e);
                } catch (r) {
                  t = {};
                }
                return t;
              }));
            case 1:
            case "end":
              return n.stop();
          }
        }, e);
      }));
      return d.apply(this, arguments);
    }
    function s() {
      var e = "TikTok";
      var t = {};
      var r = function t(r) {
        return "[".concat(e, "] ").concat(r.toString());
      };
      ["error", "log", "info"].forEach(function (e) {
        t[e] = function (t) {
          if ("info" === e) void 0;else void 0;
        };
      });
      return t;
    }
    var v = s();
    function l(e) {
      var t = e.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
      var r = new RegExp("[\\?&]".concat(t, "=([^&#]*)"));
      var n = r.exec(location.search);
      return null === n ? "" : decodeURIComponent(n[1].replace(/\+/g, " "));
    }
    function b() {
      return  false || "production" === "dev";
    }
  },
  "23d432718b3e8ce32362": function d432718b3e8ce32362(e, t, r) {
    var n = r("1f3157d073736826d4fa");
    var a = Math.max;
    var c = Math.min;
    e.exports = function (e, t) {
      e = n(e);
      return e < 0 ? a(e + t, 0) : c(e, t);
    };
  },
  "273d2eedcd369c189e70": function d2eedcd369c189e70(e, t) {
    var r = 0;
    var n = Math.random();
    e.exports = function (e) {
      return "Symbol(".concat(void 0 === e ? "" : e, ")_", (++r + n).toString(36));
    };
  },
  "2c09af3fb5c4ba3698b3": function c09af3fb5c4ba3698b3(e, t, r) {
    var n = function (e) {
      "use strict";

      var t = Object.prototype;
      var r = t.hasOwnProperty;
      var n;
      var a = "function" === typeof Symbol ? Symbol : {};
      var c = a.iterator || "@@iterator";
      var o = a.asyncIterator || "@@asyncIterator";
      var i = a.toStringTag || "@@toStringTag";
      function f(e, t, r, n) {
        var a = t && t.prototype instanceof h ? t : h;
        var c = Object.create(a.prototype);
        var o = new O(n || []);
        c._invoke = j(e, r, o);
        return c;
      }
      e.wrap = f;
      function u(e, t, r) {
        try {
          return {
            type: "normal",
            arg: e.call(t, r)
          };
        } catch (n) {
          return {
            type: "throw",
            arg: n
          };
        }
      }
      var d = "suspendedStart";
      var s = "suspendedYield";
      var v = "executing";
      var l = "completed";
      var b = {};
      function h() {}
      function p() {}
      function y() {}
      var m = {};
      m[c] = function () {
        return this;
      };
      var g = Object.getPrototypeOf;
      var w = g && g(g(P([])));
      if (w && w !== t && r.call(w, c)) m = w;
      var x = y.prototype = h.prototype = Object.create(m);
      p.prototype = x.constructor = y;
      y.constructor = p;
      y[i] = p.displayName = "GeneratorFunction";
      function _(e) {
        ["next", "throw", "return"].forEach(function (t) {
          e[t] = function (e) {
            return this._invoke(t, e);
          };
        });
      }
      e.isGeneratorFunction = function (e) {
        var t = "function" === typeof e && e.constructor;
        return t ? t === p || "GeneratorFunction" === (t.displayName || t.name) : false;
      };
      e.mark = function (e) {
        if (Object.setPrototypeOf) Object.setPrototypeOf(e, y);else {
          e.__proto__ = y;
          if (!(i in e)) e[i] = "GeneratorFunction";
        }
        e.prototype = Object.create(x);
        return e;
      };
      e.awrap = function (e) {
        return {
          __await: e
        };
      };
      function k(e, t) {
        function n(a, c, o, i) {
          var f = u(e[a], e, c);
          if ("throw" === f.type) i(f.arg);else {
            var d = f.arg;
            var s = d.value;
            if (s && "object" === _typeof(s) && r.call(s, "__await")) return t.resolve(s.__await).then(function (e) {
              n("next", e, o, i);
            }, function (e) {
              n("throw", e, o, i);
            });
            return t.resolve(s).then(function (e) {
              d.value = e;
              o(d);
            }, function (e) {
              return n("throw", e, o, i);
            });
          }
        }
        var a;
        function c(e, r) {
          function c() {
            return new t(function (t, a) {
              n(e, r, t, a);
            });
          }
          return a = a ? a.then(c, c) : c();
        }
        this._invoke = c;
      }
      _(k.prototype);
      k.prototype[o] = function () {
        return this;
      };
      e.AsyncIterator = k;
      e.async = function (t, r, n, a, c) {
        if (void 0 === c) c = Promise;
        var o = new k(f(t, r, n, a), c);
        return e.isGeneratorFunction(r) ? o : o.next().then(function (e) {
          return e.done ? e.value : o.next();
        });
      };
      function j(e, t, r) {
        var n = d;
        return function a(c, o) {
          if (n === v) throw new Error("Generator is already running");
          if (n === l) {
            if ("throw" === c) throw o;
            return T();
          }
          r.method = c;
          r.arg = o;
          while (true) {
            var i = r.delegate;
            if (i) {
              var f = E(i, r);
              if (f) {
                if (f === b) continue;
                return f;
              }
            }
            if ("next" === r.method) r.sent = r._sent = r.arg;else if ("throw" === r.method) {
              if (n === d) {
                n = l;
                throw r.arg;
              }
              r.dispatchException(r.arg);
            } else if ("return" === r.method) r.abrupt("return", r.arg);
            n = v;
            var h = u(e, t, r);
            if ("normal" === h.type) {
              n = r.done ? l : s;
              if (h.arg === b) continue;
              return {
                value: h.arg,
                done: r.done
              };
            } else if ("throw" === h.type) {
              n = l;
              r.method = "throw";
              r.arg = h.arg;
            }
          }
        };
      }
      function E(e, t) {
        var r = e.iterator[t.method];
        if (r === n) {
          t.delegate = null;
          if ("throw" === t.method) {
            if (e.iterator["return"]) {
              t.method = "return";
              t.arg = n;
              E(e, t);
              if ("throw" === t.method) return b;
            }
            t.method = "throw";
            t.arg = new TypeError("The iterator does not provide a 'throw' method");
          }
          return b;
        }
        var a = u(r, e.iterator, t.arg);
        if ("throw" === a.type) {
          t.method = "throw";
          t.arg = a.arg;
          t.delegate = null;
          return b;
        }
        var c = a.arg;
        if (!c) {
          t.method = "throw";
          t.arg = new TypeError("iterator result is not an object");
          t.delegate = null;
          return b;
        }
        if (c.done) {
          t[e.resultName] = c.value;
          t.next = e.nextLoc;
          if ("return" !== t.method) {
            t.method = "next";
            t.arg = n;
          }
        } else return c;
        t.delegate = null;
        return b;
      }
      _(x);
      x[i] = "Generator";
      x[c] = function () {
        return this;
      };
      x.toString = function () {
        return "[object Generator]";
      };
      function S(e) {
        var t = {
          tryLoc: e[0]
        };
        if (1 in e) t.catchLoc = e[1];
        if (2 in e) {
          t.finallyLoc = e[2];
          t.afterLoc = e[3];
        }
        this.tryEntries.push(t);
      }
      function A(e) {
        var t = e.completion || {};
        t.type = "normal";
        delete t.arg;
        e.completion = t;
      }
      function O(e) {
        this.tryEntries = [{
          tryLoc: "root"
        }];
        e.forEach(S, this);
        this.reset(true);
      }
      e.keys = function (e) {
        var t = [];
        for (var r in e) t.push(r);
        t.reverse();
        return function r() {
          while (t.length) {
            var n = t.pop();
            if (n in e) {
              r.value = n;
              r.done = false;
              return r;
            }
          }
          r.done = true;
          return r;
        };
      };
      function P(e) {
        if (e) {
          var t = e[c];
          if (t) return t.call(e);
          if ("function" === typeof e.next) return e;
          if (!isNaN(e.length)) {
            var a = -1,
              o = function t() {
                while (++a < e.length) if (r.call(e, a)) {
                  t.value = e[a];
                  t.done = false;
                  return t;
                }
                t.value = n;
                t.done = true;
                return t;
              };
            return o.next = o;
          }
        }
        return {
          next: T
        };
      }
      e.values = P;
      function T() {
        return {
          value: n,
          done: true
        };
      }
      O.prototype = {
        constructor: O,
        reset: function reset(e) {
          this.prev = 0;
          this.next = 0;
          this.sent = this._sent = n;
          this.done = false;
          this.delegate = null;
          this.method = "next";
          this.arg = n;
          this.tryEntries.forEach(A);
          if (!e) for (var t in this) if ("t" === t.charAt(0) && r.call(this, t) && !isNaN(+t.slice(1))) this[t] = n;
        },
        stop: function stop() {
          this.done = true;
          var e = this.tryEntries[0];
          var t = e.completion;
          if ("throw" === t.type) throw t.arg;
          return this.rval;
        },
        dispatchException: function dispatchException(e) {
          if (this.done) throw e;
          var t = this;
          function a(r, a) {
            i.type = "throw";
            i.arg = e;
            t.next = r;
            if (a) {
              t.method = "next";
              t.arg = n;
            }
            return !!a;
          }
          for (var c = this.tryEntries.length - 1; c >= 0; --c) {
            var o = this.tryEntries[c];
            var i = o.completion;
            if ("root" === o.tryLoc) return a("end");
            if (o.tryLoc <= this.prev) {
              var f = r.call(o, "catchLoc");
              var u = r.call(o, "finallyLoc");
              if (f && u) {
                if (this.prev < o.catchLoc) return a(o.catchLoc, true);else if (this.prev < o.finallyLoc) return a(o.finallyLoc);
              } else if (f) {
                if (this.prev < o.catchLoc) return a(o.catchLoc, true);
              } else if (u) {
                if (this.prev < o.finallyLoc) return a(o.finallyLoc);
              } else throw new Error("try statement without catch or finally");
            }
          }
        },
        abrupt: function abrupt(e, t) {
          for (var n = this.tryEntries.length - 1; n >= 0; --n) {
            var a = this.tryEntries[n];
            if (a.tryLoc <= this.prev && r.call(a, "finallyLoc") && this.prev < a.finallyLoc) {
              var c = a;
              break;
            }
          }
          if (c && ("break" === e || "continue" === e) && c.tryLoc <= t && t <= c.finallyLoc) c = null;
          var o = c ? c.completion : {};
          o.type = e;
          o.arg = t;
          if (c) {
            this.method = "next";
            this.next = c.finallyLoc;
            return b;
          }
          return this.complete(o);
        },
        complete: function complete(e, t) {
          if ("throw" === e.type) throw e.arg;
          if ("break" === e.type || "continue" === e.type) this.next = e.arg;else if ("return" === e.type) {
            this.rval = this.arg = e.arg;
            this.method = "return";
            this.next = "end";
          } else if ("normal" === e.type && t) this.next = t;
          return b;
        },
        finish: function finish(e) {
          for (var t = this.tryEntries.length - 1; t >= 0; --t) {
            var r = this.tryEntries[t];
            if (r.finallyLoc === e) {
              this.complete(r.completion, r.afterLoc);
              A(r);
              return b;
            }
          }
        },
        "catch": function _catch(e) {
          for (var t = this.tryEntries.length - 1; t >= 0; --t) {
            var r = this.tryEntries[t];
            if (r.tryLoc === e) {
              var n = r.completion;
              if ("throw" === n.type) {
                var a = n.arg;
                A(r);
              }
              return a;
            }
          }
          throw new Error("illegal catch attempt");
        },
        delegateYield: function delegateYield(e, t, r) {
          this.delegate = {
            iterator: P(e),
            resultName: t,
            nextLoc: r
          };
          if ("next" === this.method) this.arg = n;
          return b;
        }
      };
      return e;
    }( true ? e.exports : 0);
    try {
      regeneratorRuntime = n;
    } catch (a) {
      Function("r", "regeneratorRuntime = r")(n);
    }
  },
  "2d13d93c336705cd8ff9": function d13d93c336705cd8ff9(e, t, r) {
    var n = r("1139de1233fd67f45bd7");
    var a = r("5925dad3c5243ffee3db");
    var c = r("5f25c015bbbf9d42c661");
    var o = r("88db22626e6c88b175cf");
    var i = r("0e717e1f47872428608d");
    var f = "prototype";
    var _u = function u(e, t, r) {
      var d = e & _u.F;
      var s = e & _u.G;
      var v = e & _u.S;
      var l = e & _u.P;
      var b = e & _u.B;
      var h = s ? n : v ? n[t] || (n[t] = {}) : (n[t] || {})[f];
      var p = s ? a : a[t] || (a[t] = {});
      var y = p[f] || (p[f] = {});
      var m, g, w, x;
      if (s) r = t;
      for (m in r) {
        g = !d && h && void 0 !== h[m];
        w = (g ? h : r)[m];
        x = b && g ? i(w, n) : l && "function" == typeof w ? i(Function.call, w) : w;
        if (h) o(h, m, w, e & _u.U);
        if (p[m] != w) c(p, m, x);
        if (l && y[m] != w) y[m] = w;
      }
    };
    n.core = a;
    _u.F = 1;
    _u.G = 2;
    _u.S = 4;
    _u.P = 8;
    _u.B = 16;
    _u.W = 32;
    _u.U = 64;
    _u.R = 128;
    e.exports = _u;
  },
  "307014e72ed12da69e15": function e72ed12da69e15(e, t, r) {
    "use strict";

    var n = r("a5e6f0092ff0290e0bcf");
    var a = r("dabaabbe8ba08d59975f");
    var c = r("224ae09e536c09e69a0a");
    var o = r("efae14a3f8cd9a616256");
    e.exports = r("429cfc8e3ed333e40618")(Array, "Array", function (e, t) {
      this._t = o(e);
      this._i = 0;
      this._k = t;
    }, function () {
      var e = this._t;
      var t = this._k;
      var r = this._i++;
      if (!e || r >= e.length) {
        this._t = void 0;
        return a(1);
      }
      if ("keys" == t) return a(0, r);
      if ("values" == t) return a(0, e[r]);
      return a(0, [r, e[r]]);
    }, "values");
    c.Arguments = c.Array;
    n("keys");
    n("values");
    n("entries");
  },
  "3177845424933048caec": function caec(e, t, r) {
    "use strict";

    r.r(t);
    var n = r("3fe06cb9615786e0ea67");
    var a = r("e0b8897ffbf48a102fbb");
    var c, o;
    try {
      c = window.localStorage;
      o = window.sessionStorage;
    } catch (p) {}
    function i(e) {
      function t() {
        if (!o) return false;
        var e = "".concat(a["k"], "storage_test");
        var t = "";
        try {
          t = o.getItem(e);
          if (!t) o.setItem(e, true);
        } catch (r) {
          o.setItem(e, "");
        }
        return Boolean(t);
      }
      this.getItem = function (r) {
        var n = "";
        if (t()) n = e.getItem("".concat(a["k"]).concat(r));
        return n;
      };
      this.setItem = function (r, n) {
        if (t()) e.setItem("".concat(a["k"]).concat(r), n);
      };
      this.removeItem = function (r) {
        if (t()) e.removeItem("".concat(a["k"]).concat(r));
      };
    }
    var f = new i(c);
    var u = new i(o);
    var d = r("23617503340803072f22");
    function s(e, t) {
      if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
    }
    function v(e, t) {
      for (var r = 0; r < t.length; r++) {
        var n = t[r];
        n.enumerable = n.enumerable || false;
        n.configurable = true;
        if ("value" in n) n.writable = true;
        Object.defineProperty(e, n.key, n);
      }
    }
    function l(e, t, r) {
      if (t) v(e.prototype, t);
      if (r) v(e, r);
      Object.defineProperty(e, "prototype", {
        writable: false
      });
      return e;
    }
    function b(e, t, r, n, a, c, o) {
      try {
        var i = e[c](o);
        var f = i.value;
      } catch (u) {
        r(u);
        return;
      }
      if (i.done) t(f);else Promise.resolve(f).then(n, a);
    }
    function h(e) {
      return function () {
        var t = this,
          r = arguments;
        return new Promise(function (n, a) {
          var c = e.apply(t, r);
          function o(e) {
            b(c, n, a, o, i, "next", e);
          }
          function i(e) {
            b(c, n, a, o, i, "throw", e);
          }
          o(void 0);
        });
      };
    }
    (function () {
      var e = h(_regeneratorRuntime().mark(function e(t) {
        var r, n, c, o;
        return _regeneratorRuntime().wrap(function e(i) {
          while (1) switch (i.prev = i.next) {
            case 0:
              r = t;
              n = "true";
              c = function () {
                function e() {
                  s(this, e);
                  this.mountStatus = false;
                }
                l(e, [{
                  key: "mount",
                  value: function () {
                    var e = h(_regeneratorRuntime().mark(function e() {
                      var t, r;
                      return _regeneratorRuntime().wrap(function e(a) {
                        while (1) switch (a.prev = a.next) {
                          case 0:
                            t = this.mountStatus === n;
                            if (!t) {
                              a.next = 5;
                              break;
                            }
                            this.setNewMount(true);
                            a.next = 14;
                            break;
                          case 5:
                            this.mountStatus = true;
                            a.next = 8;
                            return this.checkLib();
                          case 8:
                            r = a.sent;
                            if (!r) {
                              a.next = 14;
                              break;
                            }
                            a.next = 12;
                            return this.libHandle();
                          case 12:
                            this.checkNewMount();
                            this.mountStatus = false;
                          case 14:
                          case "end":
                            return a.stop();
                        }
                      }, e, this);
                    }));
                    return function t() {
                      return e.apply(this, arguments);
                    };
                  }()
                }, {
                  key: "checkLib",
                  value: function () {
                    var e = h(_regeneratorRuntime().mark(function e() {
                      var t, n;
                      return _regeneratorRuntime().wrap(function e(c) {
                        while (1) switch (c.prev = c.next) {
                          case 0:
                            t = false;
                            if (!r[a["j"]]) r[a["j"]] = {};
                            n = r[a["j"]].version;
                            if (!n) {
                              n = this.getLibVersion();
                              r[a["j"]].version = n;
                            }
                            c.prev = 4;
                            c.next = 7;
                            return Promise.all([this.checkCSS(n), this.checkScript(n)]);
                          case 7:
                            t = c.sent;
                            c.next = 14;
                            break;
                          case 10:
                            c.prev = 10;
                            c.t0 = c["catch"](4);
                            t = false;
                            d["a"].error(c.t0);
                          case 14:
                            return c.abrupt("return", t);
                          case 15:
                          case "end":
                            return c.stop();
                        }
                      }, e, this, [[4, 10]]);
                    }));
                    return function t() {
                      return e.apply(this, arguments);
                    };
                  }()
                }, {
                  key: "libHandle",
                  value: function () {
                    var e = h(_regeneratorRuntime().mark(function e() {
                      var t, n, c, o, i;
                      return _regeneratorRuntime().wrap(function e(f) {
                        while (1) switch (f.prev = f.next) {
                          case 0:
                            t = r[a["j"]] || {}, n = t.lib, c = t.isEventsInit, o = void 0 === c ? false : c;
                            if (o) {
                              f.next = 6;
                              break;
                            }
                            f.t0 = n;
                            if (!f.t0) {
                              f.next = 6;
                              break;
                            }
                            f.next = 6;
                            return n.init();
                          case 6:
                            i = this.collectNodes();
                            f.t1 = n;
                            if (!f.t1) {
                              f.next = 11;
                              break;
                            }
                            f.next = 11;
                            return n.render(i);
                          case 11:
                          case "end":
                            return f.stop();
                        }
                      }, e, this);
                    }));
                    return function t() {
                      return e.apply(this, arguments);
                    };
                  }()
                }, {
                  key: "collectNodes",
                  value: function e() {
                    var t = document.getElementsByClassName(a["i"]);
                    var r = [];
                    if (t.length) r = Array.prototype.filter.call(t, function (e) {
                      var t = e.nodeName.toLowerCase() === a["l"];
                      var r = !e.id;
                      return t && r;
                    });
                    return r;
                  }
                }, {
                  key: "getLibVersion",
                  value: function e() {
                    return a["e"];
                  }
                }, {
                  key: "checkCSS",
                  value: function e(t) {
                    return new Promise(function (e) {
                      var r = document.getElementById(a["c"]);
                      if (r) e(true);else {
                        var n = document.createElement("link");
                        n.rel = "stylesheet";
                        n.type = "text/css";
                        n.id = a["c"];
                        n.href = "".concat(a["b"], "/").concat(a["f"]).concat(t, ".css");
                        document.head.appendChild(n);
                        n.onload = function () {
                          e(true);
                        };
                        n.onerror = function (t) {
                          d["a"].error(t);
                          e(false);
                        };
                      }
                    });
                  }
                }, {
                  key: "checkScript",
                  value: function e(t) {
                    return new Promise(function (e) {
                      var r = document.getElementById(a["d"]);
                      if (r) e(true);else {
                        var n = document.createElement("script");
                        n.type = "text/javascript";
                        n.id = a["d"];
                        n.src = "".concat(a["b"], "/").concat(a["f"]).concat(t, ".js");
                        document.body.appendChild(n);
                        n.onload = function () {
                          e(true);
                        };
                        n.onerror = function (t) {
                          d["a"].error(t);
                          e(false);
                        };
                      }
                    });
                  }
                }, {
                  key: "checkNewMount",
                  value: function e() {
                    var t = u.getItem(a["h"]) || "";
                    var r = t === n;
                    if (r) {
                      this.mount();
                      this.setNewMount(false);
                    }
                  }
                }, {
                  key: "setNewMount",
                  value: function e() {
                    var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "";
                    u.setItem(a["h"], t);
                  }
                }, {
                  key: "mountStatus",
                  set: function e() {
                    var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "";
                    u.setItem(a["g"], t);
                  },
                  get: function e() {
                    return u.getItem(a["g"]) || "";
                  }
                }]);
                return e;
              }();
              o = function () {
                var e = h(_regeneratorRuntime().mark(function e() {
                  var t;
                  return _regeneratorRuntime().wrap(function e(r) {
                    while (1) switch (r.prev = r.next) {
                      case 0:
                        t = new c();
                        r.next = 3;
                        return t.mount();
                      case 3:
                      case "end":
                        return r.stop();
                    }
                  }, e);
                }));
                return function t() {
                  return e.apply(this, arguments);
                };
              }();
              setTimeout(o, 0);
            case 5:
            case "end":
              return i.stop();
          }
        }, e);
      }));
      return function t(r) {
        return e.apply(this, arguments);
      };
    })()(window);
  },
  "32b716d95af2fd3c5c23": function b716d95af2fd3c5c23(e, t, r) {
    var n = r("0116c75136b233002fb9");
    var a = r("621a65702ee1a566fd32")("toStringTag");
    var c = "Arguments" == n(function () {
      return arguments;
    }());
    var o = function o(e, t) {
      try {
        return e[t];
      } catch (r) {}
    };
    e.exports = function (e) {
      var t, r, i;
      return void 0 === e ? "Undefined" : null === e ? "Null" : "string" == typeof (r = o(t = Object(e), a)) ? r : c ? n(t) : "Object" == (i = n(t)) && "function" == typeof t.callee ? "Arguments" : i;
    };
  },
  "340dbda6fe6f17098a91": function dbda6fe6f17098a91(e, t, r) {
    "use strict";

    var n = r("770f76c876afc046e4e0")(true);
    r("429cfc8e3ed333e40618")(String, "String", function (e) {
      this._t = String(e);
      this._i = 0;
    }, function () {
      var e = this._t;
      var t = this._i;
      var r;
      if (t >= e.length) return {
        value: void 0,
        done: true
      };
      r = n(e, t);
      this._i += r.length;
      return {
        value: r,
        done: false
      };
    });
  },
  "380c0734c088ca78ccda": function c0734c088ca78ccda(e, t) {
    e.exports = "constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf".split(",");
  },
  "3932316ffef991f5d157": function ffef991f5d157(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("ecd0e0fb36455021f082")(5);
    var c = "find";
    var o = true;
    if (c in []) Array(1)[c](function () {
      o = false;
    });
    n(n.P + n.F * o, "Array", {
      find: function e(t) {
        return a(this, t, arguments.length > 1 ? arguments[1] : void 0);
      }
    });
    r("a5e6f0092ff0290e0bcf")(c);
  },
  "3f2012daa60f226e86ec": function f2012daa60f226e86ec(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("8de492c765fbfb624515");
    n(n.S + n.F * r("7a7739dd8a198a2cfcb5")(function () {
      function e() {}
      return !(Array.of.call(e) instanceof e);
    }), "Array", {
      of: function e() {
        var t = 0;
        var r = arguments.length;
        var n = new ("function" == typeof this ? this : Array)(r);
        while (r > t) a(n, t, arguments[t++]);
        n.length = r;
        return n;
      }
    });
  },
  "3fc8b4ca32b0aebd9ff3": function fc8b4ca32b0aebd9ff3(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("ecd0e0fb36455021f082")(6);
    var c = "findIndex";
    var o = true;
    if (c in []) Array(1)[c](function () {
      o = false;
    });
    n(n.P + n.F * o, "Array", {
      findIndex: function e(t) {
        return a(this, t, arguments.length > 1 ? arguments[1] : void 0);
      }
    });
    r("a5e6f0092ff0290e0bcf")(c);
  },
  "3fe06cb9615786e0ea67": function fe06cb9615786e0ea67(e, t, r) {
    window.regeneratorRuntime = r("2c09af3fb5c4ba3698b3");
    r("a6444c69152f1487917b");
    if ("undefined" === typeof Promise) {
      r("9fbadcf2c98d054f245e").enable();
      window.Promise = r("e6615fafe8e11b81e37a");
    }
  },
  "40b064d4eb439c0c1c2b": function b064d4eb439c0c1c2b(e, t, r) {
    "use strict";

    var n = r("7a7739dd8a198a2cfcb5");
    e.exports = function (e, t) {
      return !!e && n(function () {
        t ? e.call(null, function () {}, 1) : e.call(null);
      });
    };
  },
  "40b8419e3e2132ef0a63": function b8419e3e2132ef0a63(e) {
    e.exports = {
      name: "tiktok_embed",
      version: "1.0.11",
      embedVersion: "1.0.11",
      description: "TikTok Embed SDK",
      main: "index.js",
      scripts: {
        test: "jest --forceExit",
        dev: "eden start",
        build: "eden build",
        "build:gcp": "NODE_REGION=GCP eden build",
        "build:dev": "eden build -a",
        commit: "eden push",
        feature: "eden feature",
        mr: "eden release -m",
        lint: "eden-lint ./src --fix"
      },
      keywords: ["tiktok", "embed"],
      author: "yangminghui.jasmine, chloe.chao",
      license: "ISC",
      devDependencies: {
        "@ies/create-eden-config": "^1.0.2",
        "@ies/eden-lint": "^2.15.4",
        "@testing-library/dom": "^8.17.1",
        "@testing-library/jest-dom": "^5.16.5",
        jest: "^28.1.3",
        jsdom: "^20.0.0"
      },
      dependencies: {
        "core-js": "2.5.7",
        promise: "^8.0.3",
        "regenerator-runtime": "^0.13.3"
      },
      husky: {
        hooks: {
          "commit-msg": "commitlint .commitlintrc.js -E HUSKY_GIT_PARAMS",
          "pre-commit": "lint-staged"
        }
      },
      "lint-staged": {
        "*": ["eden lint format", "git add"]
      }
    };
  },
  "429cfc8e3ed333e40618": function cfc8e3ed333e40618(e, t, r) {
    "use strict";

    var n = r("46a1a30d151cac60057c");
    var a = r("2d13d93c336705cd8ff9");
    var c = r("88db22626e6c88b175cf");
    var o = r("5f25c015bbbf9d42c661");
    var i = r("224ae09e536c09e69a0a");
    var f = r("5c5d334c57135891e397");
    var u = r("62828dc3ffa96c06b7c9");
    var d = r("12323e1413cbffdabe02");
    var s = r("621a65702ee1a566fd32")("iterator");
    var v = !([].keys && "next" in [].keys());
    var l = "@@iterator";
    var b = "keys";
    var h = "values";
    var p = function p() {
      return this;
    };
    e.exports = function (e, t, r, y, m, g, w) {
      f(r, t, y);
      var x = function x(e) {
        if (!v && e in E) return E[e];
        switch (e) {
          case b:
            return function t() {
              return new r(this, e);
            };
          case h:
            return function t() {
              return new r(this, e);
            };
        }
        return function t() {
          return new r(this, e);
        };
      };
      var _ = t + " Iterator";
      var k = m == h;
      var j = false;
      var E = e.prototype;
      var S = E[s] || E[l] || m && E[m];
      var A = S || x(m);
      var O = m ? !k ? A : x("entries") : void 0;
      var P = "Array" == t ? E.entries || S : S;
      var T, I, R;
      if (P) {
        R = d(P.call(new e()));
        if (R !== Object.prototype && R.next) {
          u(R, _, true);
          if (!n && "function" != typeof R[s]) o(R, s, p);
        }
      }
      if (k && S && S.name !== h) {
        j = true;
        A = function e() {
          return S.call(this);
        };
      }
      if ((!n || w) && (v || j || !E[s])) o(E, s, A);
      i[t] = A;
      i[_] = p;
      if (m) {
        T = {
          values: k ? A : x(h),
          keys: g ? A : x(b),
          entries: O
        };
        if (w) {
          for (I in T) if (!(I in E)) c(E, I, T[I]);
        } else a(a.P + a.F * (v || j), t, T);
      }
      return T;
    };
  },
  "43382cd620aa5a0df057": function cd620aa5a0df057(e, t) {
    var r;
    r = function () {
      return this;
    }();
    try {
      r = r || Function("return this")() || (1, eval)("this");
    } catch (n) {
      if ("object" === (typeof window === "undefined" ? "undefined" : _typeof(window))) r = window;
    }
    e.exports = r;
  },
  "4357234160788ed77205": function ed77205(e, t, r) {
    var n = r("79c91dcd8cd554c59c7b");
    e.exports = function (e, t, r, a) {
      try {
        return a ? t(n(r)[0], r[1]) : t(r);
      } catch (o) {
        var c = e["return"];
        if (void 0 !== c) n(c.call(e));
        throw o;
      }
    };
  },
  "43a62b7f9a94dda95bf8": function a62b7f9a94dda95bf8(e, t, r) {
    var n = r("5925dad3c5243ffee3db");
    var a = r("1139de1233fd67f45bd7");
    var c = "__core-js_shared__";
    var o = a[c] || (a[c] = {});
    (e.exports = function (e, t) {
      return o[e] || (o[e] = void 0 !== t ? t : {});
    })("versions", []).push({
      version: n.version,
      mode: r("46a1a30d151cac60057c") ? "pure" : "global",
      copyright: "\xa9 2018 Denis Pushkarev (zloirock.ru)"
    });
  },
  "46a1a30d151cac60057c": function a1a30d151cac60057c(e, t) {
    e.exports = false;
  },
  "481c1f0e850b0c8d0f7d": function c1f0e850b0c8d0f7d(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("1e0f00d296712713afe6");
    n(n.P + n.F * !r("40b064d4eb439c0c1c2b")([].reduce, true), "Array", {
      reduce: function e(t) {
        return a(this, t, arguments.length, arguments[1], false);
      }
    });
  },
  "4cc77e026250beeef142": function cc77e026250beeef142(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("efae14a3f8cd9a616256");
    var c = [].join;
    n(n.P + n.F * (r("a537d0accb907bf9d41f") != Object || !r("40b064d4eb439c0c1c2b")(c)), "Array", {
      join: function e(t) {
        return c.call(a(this), void 0 === t ? "," : t);
      }
    });
  },
  "4d073e2fededcdd3f3d3": function d073e2fededcdd3f3d3(e, t) {
    e.exports = function (e) {
      if ("function" != typeof e) throw TypeError(e + " is not a function!");
      return e;
    };
  },
  "4ef7a75a1d1957a7c5c0": function ef7a75a1d1957a7c5c0(e, t, r) {
    var n = r("a459ab805827640a27e8");
    e.exports = function (e, t) {
      if (!n(e)) return e;
      var r, a;
      if (t && "function" == typeof (r = e.toString) && !n(a = r.call(e))) return a;
      if ("function" == typeof (r = e.valueOf) && !n(a = r.call(e))) return a;
      if (!t && "function" == typeof (r = e.toString) && !n(a = r.call(e))) return a;
      throw TypeError("Can't convert object to primitive value");
    };
  },
  "574ea81f94fc0b2f332a": function ea81f94fc0b2f332a(e, t, r) {
    var n = r("621a65702ee1a566fd32")("iterator");
    var a = false;
    try {
      var c = [7][n]();
      c["return"] = function () {
        a = true;
      };
      Array.from(c, function () {
        throw 2;
      });
    } catch (o) {}
    e.exports = function (e, t) {
      if (!t && !a) return false;
      var r = false;
      try {
        var c = [7];
        var i = c[n]();
        i.next = function () {
          return {
            done: r = true
          };
        };
        c[n] = function () {
          return i;
        };
        e(c);
      } catch (o) {}
      return r;
    };
  },
  "5925dad3c5243ffee3db": function dad3c5243ffee3db(e, t) {
    var r = e.exports = {
      version: "2.5.7"
    };
    if ("number" == typeof __e) __e = r;
  },
  "5975c7ea3ecd56e534e3": function c7ea3ecd56e534e3(e, t, r) {
    e.exports = !r("7a7739dd8a198a2cfcb5")(function () {
      return 7 != Object.defineProperty({}, "a", {
        get: function get() {
          return 7;
        }
      }).a;
    });
  },
  "5b9dc0e027f5c68eff1a": function b9dc0e027f5c68eff1a(e, t, r) {
    var n = r("1139de1233fd67f45bd7").document;
    e.exports = n && n.documentElement;
  },
  "5baa1143f58e62614ca0": function baa1143f58e62614ca0(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("4d073e2fededcdd3f3d3");
    var c = r("d394d0a4c1838202489d");
    var o = r("7a7739dd8a198a2cfcb5");
    var i = [].sort;
    var f = [1, 2, 3];
    n(n.P + n.F * (o(function () {
      f.sort(void 0);
    }) || !o(function () {
      f.sort(null);
    }) || !r("40b064d4eb439c0c1c2b")(i)), "Array", {
      sort: function e(t) {
        return void 0 === t ? i.call(c(this)) : i.call(c(this), a(t));
      }
    });
  },
  "5c5d334c57135891e397": function c5d334c57135891e397(e, t, r) {
    "use strict";

    var n = r("ef09ce044ac3210bc947");
    var a = r("9d5592e83ab7f3d08974");
    var c = r("62828dc3ffa96c06b7c9");
    var o = {};
    r("5f25c015bbbf9d42c661")(o, r("621a65702ee1a566fd32")("iterator"), function () {
      return this;
    });
    e.exports = function (e, t, r) {
      e.prototype = n(o, {
        next: a(1, r)
      });
      c(e, t + " Iterator");
    };
  },
  "5f25c015bbbf9d42c661": function f25c015bbbf9d42c661(e, t, r) {
    var n = r("1b01d0e59d3af79760a0");
    var a = r("9d5592e83ab7f3d08974");
    e.exports = r("5975c7ea3ecd56e534e3") ? function (e, t, r) {
      return n.f(e, t, a(1, r));
    } : function (e, t, r) {
      e[t] = r;
      return e;
    };
  },
  "5f6eded14a8ed86276fc": function f6eded14a8ed86276fc(e, t, r) {
    var n = r("43a62b7f9a94dda95bf8")("keys");
    var a = r("273d2eedcd369c189e70");
    e.exports = function (e) {
      return n[e] || (n[e] = a(e));
    };
  },
  "621a65702ee1a566fd32": function a65702ee1a566fd32(e, t, r) {
    var n = r("43a62b7f9a94dda95bf8")("wks");
    var a = r("273d2eedcd369c189e70");
    var c = r("1139de1233fd67f45bd7").Symbol;
    var o = "function" == typeof c;
    var i = e.exports = function (e) {
      return n[e] || (n[e] = o && c[e] || (o ? c : a)("Symbol." + e));
    };
    i.store = n;
  },
  "62828dc3ffa96c06b7c9": function dc3ffa96c06b7c9(e, t, r) {
    var n = r("1b01d0e59d3af79760a0").f;
    var a = r("d34390b928ecadc11dc8");
    var c = r("621a65702ee1a566fd32")("toStringTag");
    e.exports = function (e, t, r) {
      if (e && !a(e = r ? e : e.prototype, c)) n(e, c, {
        configurable: true,
        value: t
      });
    };
  },
  "68a989d6f90923909f2a": function a989d6f90923909f2a(e, t, r) {
    var n = r("a459ab805827640a27e8");
    var a = r("1139de1233fd67f45bd7").document;
    var c = n(a) && n(a.createElement);
    e.exports = function (e) {
      return c ? a.createElement(e) : {};
    };
  },
  "6ae9955278ddcf01bbfd": function ae9955278ddcf01bbfd(e, t, r) {
    var n = r("a459ab805827640a27e8");
    var a = r("8f648b1026e01323935b");
    var c = r("621a65702ee1a566fd32")("species");
    e.exports = function (e) {
      var t;
      if (a(e)) {
        t = e.constructor;
        if ("function" == typeof t && (t === Array || a(t.prototype))) t = void 0;
        if (n(t)) {
          t = t[c];
          if (null === t) t = void 0;
        }
      }
      return void 0 === t ? Array : t;
    };
  },
  "71d3ec5bd65418e28ebc": function d3ec5bd65418e28ebc(e, t, r) {
    var n = r("224ae09e536c09e69a0a");
    var a = r("621a65702ee1a566fd32")("iterator");
    var c = Array.prototype;
    e.exports = function (e) {
      return void 0 !== e && (n.Array === e || c[a] === e);
    };
  },
  "74be682203e7c82cb4c9": function be682203e7c82cb4c9(e, t, r) {
    r("89a21af31babcdc56725")("Array");
  },
  "770f76c876afc046e4e0": function f76c876afc046e4e0(e, t, r) {
    var n = r("1f3157d073736826d4fa");
    var a = r("7ad2c0eb9a8edcc472a4");
    e.exports = function (e) {
      return function (t, r) {
        var c = String(a(t));
        var o = n(r);
        var i = c.length;
        var f, u;
        if (o < 0 || o >= i) return e ? "" : void 0;
        f = c.charCodeAt(o);
        return f < 55296 || f > 56319 || o + 1 === i || (u = c.charCodeAt(o + 1)) < 56320 || u > 57343 ? e ? c.charAt(o) : f : e ? c.slice(o, o + 2) : (f - 55296 << 10) + (u - 56320) + 65536;
      };
    };
  },
  "79c91dcd8cd554c59c7b": function c91dcd8cd554c59c7b(e, t, r) {
    var n = r("a459ab805827640a27e8");
    e.exports = function (e) {
      if (!n(e)) throw TypeError(e + " is not an object!");
      return e;
    };
  },
  "7a7739dd8a198a2cfcb5": function a7739dd8a198a2cfcb5(e, t) {
    e.exports = function (e) {
      try {
        return !!e();
      } catch (t) {
        return true;
      }
    };
  },
  "7ad2c0eb9a8edcc472a4": function ad2c0eb9a8edcc472a4(e, t) {
    e.exports = function (e) {
      if (void 0 == e) throw TypeError("Can't call method on  " + e);
      return e;
    };
  },
  "88db22626e6c88b175cf": function db22626e6c88b175cf(e, t, r) {
    var n = r("1139de1233fd67f45bd7");
    var a = r("5f25c015bbbf9d42c661");
    var c = r("d34390b928ecadc11dc8");
    var o = r("273d2eedcd369c189e70")("src");
    var i = "toString";
    var f = Function[i];
    var u = ("" + f).split(i);
    r("5925dad3c5243ffee3db").inspectSource = function (e) {
      return f.call(e);
    };
    (e.exports = function (e, t, r, i) {
      var f = "function" == typeof r;
      if (f) c(r, "name") || a(r, "name", t);
      if (e[t] === r) return;
      if (f) c(r, o) || a(r, o, e[t] ? "" + e[t] : u.join(String(t)));
      if (e === n) e[t] = r;else if (!i) {
        delete e[t];
        a(e, t, r);
      } else if (e[t]) e[t] = r;else a(e, t, r);
    })(Function.prototype, i, function e() {
      return "function" == typeof this && this[o] || f.call(this);
    });
  },
  "896f33738142bf70015e": function f33738142bf70015e(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("1e0f00d296712713afe6");
    n(n.P + n.F * !r("40b064d4eb439c0c1c2b")([].reduceRight, true), "Array", {
      reduceRight: function e(t) {
        return a(this, t, arguments.length, arguments[1], true);
      }
    });
  },
  "89a21af31babcdc56725": function a21af31babcdc56725(e, t, r) {
    "use strict";

    var n = r("1139de1233fd67f45bd7");
    var a = r("1b01d0e59d3af79760a0");
    var c = r("5975c7ea3ecd56e534e3");
    var o = r("621a65702ee1a566fd32")("species");
    e.exports = function (e) {
      var t = n[e];
      if (c && t && !t[o]) a.f(t, o, {
        configurable: true,
        get: function get() {
          return this;
        }
      });
    };
  },
  "8de492c765fbfb624515": function de492c765fbfb624515(e, t, r) {
    "use strict";

    var n = r("1b01d0e59d3af79760a0");
    var a = r("9d5592e83ab7f3d08974");
    e.exports = function (e, t, r) {
      if (t in e) n.f(e, t, a(0, r));else e[t] = r;
    };
  },
  "8f648b1026e01323935b": function f648b1026e01323935b(e, t, r) {
    var n = r("0116c75136b233002fb9");
    e.exports = Array.isArray || function e(t) {
      return "Array" == n(t);
    };
  },
  "9125d51ebaa93f49cfd5": function d51ebaa93f49cfd5(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("ecd0e0fb36455021f082")(3);
    n(n.P + n.F * !r("40b064d4eb439c0c1c2b")([].some, true), "Array", {
      some: function e(t) {
        return a(this, t, arguments[1]);
      }
    });
  },
  "9d5592e83ab7f3d08974": function d5592e83ab7f3d08974(e, t) {
    e.exports = function (e, t) {
      return {
        enumerable: !(1 & e),
        configurable: !(2 & e),
        writable: !(4 & e),
        value: t
      };
    };
  },
  "9fbadcf2c98d054f245e": function fbadcf2c98d054f245e(e, t, r) {
    "use strict";

    var n = r("1b24b031f63a74d1f57f");
    var a = [ReferenceError, TypeError, RangeError];
    var c = false;
    t.disable = o;
    function o() {
      c = false;
      n._Y = null;
      n._Z = null;
    }
    t.enable = i;
    function i(e) {
      e = e || {};
      if (c) o();
      c = true;
      var t = 0;
      var r = 0;
      var i = {};
      n._Y = function (e) {
        if (2 === e._V && i[e._1]) {
          if (i[e._1].logged) s(e._1);else clearTimeout(i[e._1].timeout);
          delete i[e._1];
        }
      };
      n._Z = function (e, r) {
        if (0 === e._U) {
          e._1 = t++;
          i[e._1] = {
            displayId: null,
            error: r,
            timeout: setTimeout(d.bind(null, e._1), u(r, a) ? 100 : 2e3),
            logged: false
          };
        }
      };
      function d(t) {
        if (e.allRejections || u(i[t].error, e.whitelist || a)) {
          i[t].displayId = r++;
          if (e.onUnhandled) {
            i[t].logged = true;
            e.onUnhandled(i[t].displayId, i[t].error);
          } else {
            i[t].logged = true;
            f(i[t].displayId, i[t].error);
          }
        }
      }
      function s(t) {
        if (i[t].logged) if (e.onHandled) e.onHandled(i[t].displayId, i[t].error);else if (!i[t].onUnhandled) {
          void 0;
          void 0;
        }
      }
    }
    function f(e, t) {
      void 0;
      var r = (t && (t.stack || t)) + "";
      r.split("\n").forEach(function (e) {
        void 0;
      });
    }
    function u(e, t) {
      return t.some(function (t) {
        return e instanceof t;
      });
    }
  },
  a459ab805827640a27e8: function a459ab805827640a27e8(e, t) {
    e.exports = function (e) {
      return "object" === _typeof(e) ? null !== e : "function" === typeof e;
    };
  },
  a537d0accb907bf9d41f: function a537d0accb907bf9d41f(e, t, r) {
    var n = r("0116c75136b233002fb9");
    e.exports = Object("z").propertyIsEnumerable(0) ? Object : function (e) {
      return "String" == n(e) ? e.split("") : Object(e);
    };
  },
  a5e6f0092ff0290e0bcf: function a5e6f0092ff0290e0bcf(e, t, r) {
    var n = r("621a65702ee1a566fd32")("unscopables");
    var a = Array.prototype;
    if (void 0 == a[n]) r("5f25c015bbbf9d42c661")(a, n, {});
    e.exports = function (e) {
      a[n][e] = true;
    };
  },
  a6444c69152f1487917b: function a6444c69152f1487917b(e, t, r) {
    r("340dbda6fe6f17098a91");
    r("cff317ca62d09454b2d3");
    r("0a5193b0534d54dbcda1");
    r("3f2012daa60f226e86ec");
    r("4cc77e026250beeef142");
    r("1b71f5e96f29a92413b6");
    r("5baa1143f58e62614ca0");
    r("0f025c284bc567debf3d");
    r("1591b90b4327c94245ad");
    r("0565be87d109f51269ca");
    r("9125d51ebaa93f49cfd5");
    r("cf802dc5a6982c8247e6");
    r("481c1f0e850b0c8d0f7d");
    r("896f33738142bf70015e");
    r("c9be824f185f52a1979a");
    r("1468f5d37f30f63d76ea");
    r("bc0f72eb2321587e4779");
    r("fa5cd10f53a62f249502");
    r("3932316ffef991f5d157");
    r("3fc8b4ca32b0aebd9ff3");
    r("74be682203e7c82cb4c9");
    r("307014e72ed12da69e15");
    e.exports = r("5925dad3c5243ffee3db").Array;
  },
  a6d645856102faf17328: function a6d645856102faf17328(e, t, r) {
    var n = r("1b01d0e59d3af79760a0");
    var a = r("79c91dcd8cd554c59c7b");
    var c = r("145c4f2bdc487b2578df");
    e.exports = r("5975c7ea3ecd56e534e3") ? Object.defineProperties : function e(t, r) {
      a(t);
      var o = c(r);
      var i = o.length;
      var f = 0;
      var u;
      while (i > f) n.f(t, u = o[f++], r[u]);
      return t;
    };
  },
  bc0f72eb2321587e4779: function bc0f72eb2321587e4779(e, t, r) {
    var n = r("2d13d93c336705cd8ff9");
    n(n.P, "Array", {
      copyWithin: r("06b2b473e53cc019ca74")
    });
    r("a5e6f0092ff0290e0bcf")("copyWithin");
  },
  bc1a333d2b6eceac22e3: function bc1a333d2b6eceac22e3(e, t, r) {
    e.exports = !r("5975c7ea3ecd56e534e3") && !r("7a7739dd8a198a2cfcb5")(function () {
      return 7 != Object.defineProperty(r("68a989d6f90923909f2a")("div"), "a", {
        get: function get() {
          return 7;
        }
      }).a;
    });
  },
  c9be824f185f52a1979a: function c9be824f185f52a1979a(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("2076e982c14c41925fb4")(false);
    var c = [].indexOf;
    var o = !!c && 1 / [1].indexOf(1, -0) < 0;
    n(n.P + n.F * (o || !r("40b064d4eb439c0c1c2b")(c)), "Array", {
      indexOf: function e(t) {
        return o ? c.apply(this, arguments) || 0 : a(this, t, arguments[1]);
      }
    });
  },
  ca19e32ab6ee3b3692aa: function ca19e32ab6ee3b3692aa(e, t, r) {
    var n = r("1f3157d073736826d4fa");
    var a = Math.min;
    e.exports = function (e) {
      return e > 0 ? a(n(e), 9007199254740991) : 0;
    };
  },
  cf802dc5a6982c8247e6: function cf802dc5a6982c8247e6(e, t, r) {
    "use strict";

    var n = r("2d13d93c336705cd8ff9");
    var a = r("ecd0e0fb36455021f082")(4);
    n(n.P + n.F * !r("40b064d4eb439c0c1c2b")([].every, true), "Array", {
      every: function e(t) {
        return a(this, t, arguments[1]);
      }
    });
  },
  cff317ca62d09454b2d3: function cff317ca62d09454b2d3(e, t, r) {
    var n = r("2d13d93c336705cd8ff9");
    n(n.S, "Array", {
      isArray: r("8f648b1026e01323935b")
    });
  },
  d34390b928ecadc11dc8: function d34390b928ecadc11dc8(e, t) {
    var r = {}.hasOwnProperty;
    e.exports = function (e, t) {
      return r.call(e, t);
    };
  },
  d394d0a4c1838202489d: function d394d0a4c1838202489d(e, t, r) {
    var n = r("7ad2c0eb9a8edcc472a4");
    e.exports = function (e) {
      return Object(n(e));
    };
  },
  dabaabbe8ba08d59975f: function dabaabbe8ba08d59975f(e, t) {
    e.exports = function (e, t) {
      return {
        value: t,
        done: !!e
      };
    };
  },
  e0b8897ffbf48a102fbb: function e0b8897ffbf48a102fbb(e, t, r) {
    "use strict";

    r.d(t, "b", function () {
      return f;
    });
    r.d(t, "c", function () {
      return u;
    });
    r.d(t, "d", function () {
      return d;
    });
    r.d(t, "f", function () {
      return s;
    });
    r.d(t, "e", function () {
      return v;
    });
    r.d(t, "o", function () {
      return l;
    });
    r.d(t, "l", function () {
      return b;
    });
    r.d(t, "j", function () {
      return h;
    });
    r.d(t, "i", function () {
      return p;
    });
    r.d(t, "m", function () {
      return y;
    });
    r.d(t, "k", function () {
      return m;
    });
    r.d(t, "g", function () {
      return g;
    });
    r.d(t, "h", function () {
      return w;
    });
    r.d(t, "a", function () {
      return x;
    });
    r.d(t, "n", function () {
      return _;
    });
    var n = "production" !== "production";
    var a = "SG" === "TTP";
    var c = "SG" === "VA";
    var o = a ? "https://lf16-tiktok-web.tiktokcdn-us.com" : "https://lf16-tiktok-web.ttwstatic.com";
    var i = a ? "/obj/tiktok-web-tx/tiktok/falcon/embed" : c ? "/obj/tiktok-web-us/tiktok/falcon/embed" : "/obj/tiktok-web/tiktok/falcon/embed";
    var f = n ? "http://127.0.0.1:4000" : "".concat(o).concat(i);
    var u = "ttEmbedLibCSS";
    var d = "ttEmbedLibScript";
    var s = "embed_lib_v";
    var v = r("40b8419e3e2132ef0a63").version;
    var l = "https://www.tiktok.com";
    var b = "blockquote";
    var h = "tiktokEmbed";
    var p = "tiktok-embed";
    var y = "message";
    var m = "__tt_embed__";
    var g = "mounting";
    var w = "newmount";
    var x = Object.freeze({
      CREATOR: "creator",
      VIDEO: "video",
      TAG: "tag",
      MUSIC: "music",
      TRENDING: "trending",
      CURATED: "curated"
    });
    var _ = {
      TTP: ["https://www.tiktok.com", "https://lf16-tiktok-common.tiktokcdn-us.com", "https://vmweb.us.tiktok.com"],
      VA: ["https://www.tiktok.com", "https://lf16-tiktok-common.ttwstatic.com", "https://vmweb-va.byteoversea.com"],
      SG: ["https://www.tiktok.com", "https://lf16-tiktok-common.ttwstatic.com", "https://vmweb-sg.byteoversea.com"],
      Default: ["https://www.tiktok.com", "https://lf16-tiktok-common.tiktokcdn-us.com", "https://vmweb.us.tiktok.com"]
    };
  },
  e3136eea77d23f8d97ec: function e3136eea77d23f8d97ec(e, t, r) {
    var n = r("d34390b928ecadc11dc8");
    var a = r("efae14a3f8cd9a616256");
    var c = r("2076e982c14c41925fb4")(false);
    var o = r("5f6eded14a8ed86276fc")("IE_PROTO");
    e.exports = function (e, t) {
      var r = a(e);
      var i = 0;
      var f = [];
      var u;
      for (u in r) if (u != o) n(r, u) && f.push(u);
      while (t.length > i) if (n(r, u = t[i++])) ~c(f, u) || f.push(u);
      return f;
    };
  },
  e6615fafe8e11b81e37a: function e6615fafe8e11b81e37a(e, t, r) {
    "use strict";

    var n = r("1b24b031f63a74d1f57f");
    e.exports = n;
    var a = d(true);
    var c = d(false);
    var o = d(null);
    var i = d(void 0);
    var f = d(0);
    var u = d("");
    function d(e) {
      var t = new n(n._0);
      t._V = 1;
      t._W = e;
      return t;
    }
    n.resolve = function (e) {
      if (e instanceof n) return e;
      if (null === e) return o;
      if (void 0 === e) return i;
      if (true === e) return a;
      if (false === e) return c;
      if (0 === e) return f;
      if ("" === e) return u;
      if ("object" === _typeof(e) || "function" === typeof e) try {
        var t = e.then;
        if ("function" === typeof t) return new n(t.bind(e));
      } catch (r) {
        return new n(function (e, t) {
          t(r);
        });
      }
      return d(e);
    };
    var _s = function s(e) {
      if ("function" === typeof Array.from) {
        _s = Array.from;
        return Array.from(e);
      }
      _s = function s(e) {
        return Array.prototype.slice.call(e);
      };
      return Array.prototype.slice.call(e);
    };
    n.all = function (e) {
      var t = _s(e);
      return new n(function (e, r) {
        if (0 === t.length) return e([]);
        var a = t.length;
        function c(o, i) {
          if (i && ("object" === _typeof(i) || "function" === typeof i)) if (i instanceof n && i.then === n.prototype.then) {
            while (3 === i._V) i = i._W;
            if (1 === i._V) return c(o, i._W);
            if (2 === i._V) r(i._W);
            i.then(function (e) {
              c(o, e);
            }, r);
            return;
          } else {
            var f = i.then;
            if ("function" === typeof f) {
              var u = new n(f.bind(i));
              u.then(function (e) {
                c(o, e);
              }, r);
              return;
            }
          }
          t[o] = i;
          if (0 === --a) e(t);
        }
        for (var o = 0; o < t.length; o++) c(o, t[o]);
      });
    };
    n.reject = function (e) {
      return new n(function (t, r) {
        r(e);
      });
    };
    n.race = function (e) {
      return new n(function (t, r) {
        _s(e).forEach(function (e) {
          n.resolve(e).then(t, r);
        });
      });
    };
    n.prototype["catch"] = function (e) {
      return this.then(null, e);
    };
  },
  ecd0e0fb36455021f082: function ecd0e0fb36455021f082(e, t, r) {
    var n = r("0e717e1f47872428608d");
    var a = r("a537d0accb907bf9d41f");
    var c = r("d394d0a4c1838202489d");
    var o = r("ca19e32ab6ee3b3692aa");
    var i = r("0594430ce9777a0767aa");
    e.exports = function (e, t) {
      var r = 1 == e;
      var f = 2 == e;
      var u = 3 == e;
      var d = 4 == e;
      var s = 6 == e;
      var v = 5 == e || s;
      var l = t || i;
      return function (t, i, b) {
        var h = c(t);
        var p = a(h);
        var y = n(i, b, 3);
        var m = o(p.length);
        var g = 0;
        var w = r ? l(t, m) : f ? l(t, 0) : void 0;
        var x, _;
        for (; m > g; g++) if (v || g in p) {
          x = p[g];
          _ = y(x, g, h);
          if (e) if (r) w[g] = _;else if (_) switch (e) {
            case 3:
              return true;
            case 5:
              return x;
            case 6:
              return g;
            case 2:
              w.push(x);
          } else if (d) return false;
        }
        return s ? -1 : u || d ? d : w;
      };
    };
  },
  ef09ce044ac3210bc947: function ef09ce044ac3210bc947(e, t, r) {
    var n = r("79c91dcd8cd554c59c7b");
    var a = r("a6d645856102faf17328");
    var c = r("380c0734c088ca78ccda");
    var o = r("5f6eded14a8ed86276fc")("IE_PROTO");
    var i = function i() {};
    var f = "prototype";
    var _u2 = function u() {
      var e = r("68a989d6f90923909f2a")("iframe");
      var t = c.length;
      var n = "<";
      var a = ">";
      var o;
      e.style.display = "none";
      r("5b9dc0e027f5c68eff1a").appendChild(e);
      e.src = "javascript:";
      o = e.contentWindow.document;
      o.open();
      o.write(n + "script" + a + "document.F=Object" + n + "/script" + a);
      o.close();
      _u2 = o.F;
      while (t--) delete _u2[f][c[t]];
      return _u2();
    };
    e.exports = Object.create || function e(t, r) {
      var c;
      if (null !== t) {
        i[f] = n(t);
        c = new i();
        i[f] = null;
        c[o] = t;
      } else c = _u2();
      return void 0 === r ? c : a(c, r);
    };
  },
  efae14a3f8cd9a616256: function efae14a3f8cd9a616256(e, t, r) {
    var n = r("a537d0accb907bf9d41f");
    var a = r("7ad2c0eb9a8edcc472a4");
    e.exports = function (e) {
      return n(a(e));
    };
  },
  f88518adc3004bf8d923: function f88518adc3004bf8d923(e, t, r) {
    var n = r("32b716d95af2fd3c5c23");
    var a = r("621a65702ee1a566fd32")("iterator");
    var c = r("224ae09e536c09e69a0a");
    e.exports = r("5925dad3c5243ffee3db").getIteratorMethod = function (e) {
      if (void 0 != e) return e[a] || e["@@iterator"] || c[n(e)];
    };
  },
  f8f08bdd917755ac017a: function f8f08bdd917755ac017a(e, t, r) {
    "use strict";

    (function (t) {
      e.exports = r;
      function r(e) {
        if (!n.length) {
          c();
          a = true;
        }
        n[n.length] = e;
      }
      var n = [];
      var a = false;
      var c;
      var o = 0;
      var i = 1024;
      function f() {
        while (o < n.length) {
          var e = o;
          o += 1;
          n[e].call();
          if (o > i) {
            for (var t = 0, r = n.length - o; t < r; t++) n[t] = n[t + o];
            n.length -= o;
            o = 0;
          }
        }
        n.length = 0;
        o = 0;
        a = false;
      }
      var u = "undefined" !== typeof t ? t : self;
      var d = u.MutationObserver || u.WebKitMutationObserver;
      if ("function" === typeof d) c = s(f);else c = v(f);
      r.requestFlush = c;
      function s(e) {
        var t = 1;
        var r = new d(e);
        var n = document.createTextNode("");
        r.observe(n, {
          characterData: true
        });
        return function e() {
          t = -t;
          n.data = t;
        };
      }
      function v(e) {
        return function t() {
          var r = setTimeout(a, 0);
          var n = setInterval(a, 50);
          function a() {
            clearTimeout(r);
            clearInterval(n);
            e();
          }
        };
      }
      r.makeRequestCallFromTimer = v;
    }).call(this, r("43382cd620aa5a0df057"));
  },
  fa5cd10f53a62f249502: function fa5cd10f53a62f249502(e, t, r) {
    var n = r("2d13d93c336705cd8ff9");
    n(n.P, "Array", {
      fill: r("03192b087bb464f059c0")
    });
    r("a5e6f0092ff0290e0bcf")("fill");
  }
});
Array.prototype.slice.call(document.getElementsByClassName("ub-advanced-video-thumbnail")).forEach(function (instance) {
  instance.addEventListener("click", function () {
    instance.setAttribute("hidden", true);
    instance.nextElementSibling.removeAttribute("hidden");
  });
});

/***/ }),

/***/ "./src/blocks/button/front.js":
/*!************************************!*\
  !*** ./src/blocks/button/front.js ***!
  \************************************/
/***/ (() => {

/* eslint-disable */

Array.prototype.slice.call(document.getElementsByClassName("ub-button-block-main")).forEach(function (instance) {
  if (instance.hasAttribute("data-defaultcolor")) {
    instance.addEventListener("mouseenter", function () {
      var buttonIsTransparent = JSON.parse(instance.getAttribute("data-buttonistransparent"));
      instance.style.color = buttonIsTransparent ? instance.getAttribute("data-hovercolor") : instance.getAttribute("data-hovertextcolor");
      instance.style.backgroundColor = buttonIsTransparent ? "transparent" : instance.getAttribute("data-hovercolor");
      instance.style.border = buttonIsTransparent ? "3px solid ".concat(instance.getAttribute("data-hovercolor")) : "none";
    });
    instance.addEventListener("mouseleave", function () {
      var buttonIsTransparent = JSON.parse(instance.getAttribute("data-buttonistransparent"));
      instance.style.color = buttonIsTransparent ? instance.getAttribute("data-defaultcolor") : instance.getAttribute("data-defaulttextcolor");
      instance.style.backgroundColor = buttonIsTransparent ? "transparent" : instance.getAttribute("data-defaultcolor");
      instance.style.border = buttonIsTransparent ? "3px solid ".concat(instance.getAttribute("data-defaultcolor")) : "none";
    });
  }
});

/***/ }),

/***/ "./src/blocks/content-filter/front.js":
/*!********************************************!*\
  !*** ./src/blocks/content-filter/front.js ***!
  \********************************************/
/***/ (() => {

if (!Element.prototype.matches) {
  Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}
if (!Element.prototype.closest) {
  Element.prototype.closest = function (s) {
    var el = this;
    do {
      if (el.matches(s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);
    return null;
  };
}
function ub_getSiblings(element, criteria) {
  var children = Array.prototype.slice.call(element.parentNode.children).filter(function (child) {
    return child !== element;
  });
  return criteria ? children.filter(criteria) : children;
}
Array.prototype.slice.call(document.getElementsByClassName("ub-content-filter-tag")).forEach(function (instance) {
  var blockProper = instance.closest(".wp-block-ub-content-filter-block");
  var initialSelection = blockProper.getAttribute("data-currentselection");
  instance.addEventListener("click", function () {
    var _this = this;
    var isOldVersion = this.getAttribute("data-activecolor");
    this.setAttribute("data-tagisselected", JSON.stringify(!JSON.parse(this.getAttribute("data-tagisselected"))));
    if (isOldVersion) {
      this.style.backgroundColor = this.getAttribute("data-activecolor");
      this.style.color = this.getAttribute("data-activetextcolor");
    } else {
      this.classList.toggle("ub-selected");
    }
    var categoryIndex = JSON.parse(this.getAttribute("data-categorynumber"));
    var filterIndex = JSON.parse(this.getAttribute("data-filternumber"));
    if (JSON.parse(this.getAttribute("data-tagisselected"))) {
      if (!JSON.parse(this.parentElement.getAttribute("data-canusemultiple"))) {
        ub_getSiblings(this, function (elem) {
          return elem.classList.contains("ub-content-filter-tag");
        }).forEach(function (sibling) {
          sibling.setAttribute("data-tagisselected", "false");
          if (isOldVersion) {
            sibling.style.backgroundColor = _this.getAttribute("data-normalcolor");
            sibling.style.color = _this.getAttribute("data-normaltextcolor");
          } else {
            sibling.classList.remove("ub-selected");
          }
        });
      }
    } else {
      if (isOldVersion) {
        this.style.backgroundColor = this.getAttribute("data-normalcolor");
        this.style.color = this.getAttribute("data-normaltextcolor");
      } else {
        this.classList.remove("ub-selected");
      }
    }
    var newSelection = JSON.parse(blockProper.getAttribute("data-currentselection"));
    if (Array.isArray(newSelection[categoryIndex])) {
      newSelection[categoryIndex][filterIndex] = JSON.parse(this.getAttribute("data-tagisselected"));
    } else {
      newSelection[categoryIndex] = JSON.parse(this.getAttribute("data-tagisselected")) ? filterIndex : -1;
    }
    blockProper.setAttribute("data-currentselection", JSON.stringify(newSelection));
    var matchingOption = blockProper.getAttribute("data-matchingoption");
    Array.prototype.slice.call(blockProper.querySelectorAll(":scope > .ub-content-filter-panel")).forEach(function (instance) {
      var panelData = JSON.parse(instance.getAttribute("data-selectedfilters"));
      var mainData = JSON.parse(blockProper.getAttribute("data-currentselection"));
      var hasMatchedAll = true;
      if (initialSelection == blockProper.getAttribute("data-currentselection") && JSON.parse(blockProper.getAttribute("data-initiallyshowall")) === false) {
        hasMatchedAll = false;
      } else {
        panelData.forEach(function (category, i) {
          if (Array.isArray(category)) {
            if (category.every(function (f) {
              return !f;
            }) && mainData[i].some(function (f) {
              return f;
            }) ||
            //panel has no tag from category and maindata has at least one tag selected -> consider as mismatch
            mainData[i].filter(function (f) {
              return f;
            }).length > 0 && !category.some(function (f, j) {
              return f && f === mainData[i][j];
            })) {
              hasMatchedAll = false;
            }
          } else if (mainData[i] !== category && mainData[i] !== -1) {
            hasMatchedAll = false;
          }
        });
      }

      //alternate setting
      var hasMatchedOne = false;
      if (initialSelection === blockProper.getAttribute("data-currentselection") && JSON.parse(blockProper.getAttribute("data-initiallyshowall")) === true) {
        hasMatchedOne = true;
      } else {
        panelData.forEach(function (category, i) {
          if (Array.isArray(category)) {
            if (mainData[i].filter(function (f) {
              return f;
            }).length > 0 && category.filter(function (f, j) {
              return f && f === mainData[i][j];
            }).length > 0) {
              hasMatchedOne = true;
            }
          } else if (mainData[i] === category && mainData[i] !== -1) {
            hasMatchedOne = true;
          }
        });
      }
      var isVisible = matchingOption === "matchAll" ? hasMatchedAll : hasMatchedOne;
      if (isOldVersion) {
        instance.style.display = isVisible ? "block" : "none";
      } else if (isVisible) {
        instance.classList.remove("ub-hide");
        Array.prototype.slice.call(document.getElementsByClassName("ub_image_slider")).forEach(function (slider) {
          var swiper = new Swiper("#".concat(slider.id), JSON.parse(slider.dataset.swiperData));
        });
        setTimeout(function () {
          window.dispatchEvent(new Event("resize"));
        }, 100);
      } else {
        instance.classList.add("ub-hide");
      }
      Array.prototype.slice.call(instance.querySelectorAll(".wp-block-embed iframe")).forEach(function (embeddedContent) {
        embeddedContent.style.removeProperty("width");
        embeddedContent.style.removeProperty("height");
      });
    });
  });
});

/*Array.from(document.getElementsByClassName('ub-content-filter-reset')).forEach(
	instance => {
		instance.addEventListener('click', function() {
			const blockProper = this.closest('.wp-block-ub-content-filter');

			let blockSelection = JSON.parse(
				blockProper.getAttribute('data-currentselection')
			);

			blockSelection = blockSelection.map(c =>
				Array.isArray(c) ? Array(c.length).fill(false) : -1
			);

			blockProper.setAttribute(
				'data-currentselection',
				JSON.stringify(blockSelection)
			);

			Array.from(
				blockProper.getElementsByClassName('ub-content-filter-panel')
			).forEach(instance => {
				instance.style.display = 'block';
			});

			Array.from(
				blockProper.getElementsByClassName('ub-content-filter-tag')
			).forEach(instance => {
				instance.setAttribute('data-tagisselected', 'false');
				instance.style.backgroundColor = instance.getAttribute(
					'data-normalcolor'
				);
				instance.style.color = instance.getAttribute(
					'data-normaltextcolor'
				);
			});
		});
	}
);*/

/***/ }),

/***/ "./src/blocks/content-toggle/front.js":
/*!********************************************!*\
  !*** ./src/blocks/content-toggle/front.js ***!
  \********************************************/
/***/ (() => {

function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
var convertToPixels = function convertToPixels(amount, unit) {
  return unit === "%" ? amount / 100 * window.innerWidth : amount;
};
var togglePanel = function togglePanel(target) {
  var topPadding = 0,
    bottomPadding = 0;
  var indicator = target.querySelector(".wp-block-ub-content-toggle-accordion-state-indicator");
  var panelContent = target.nextElementSibling;
  var toggleContainer = target.closest(".wp-block-ub-content-toggle");
  if (panelContent.classList.contains("ub-hide")) {
    var computedStyles = getComputedStyle(panelContent);
    var topPaddingUnit = computedStyles.paddingTop.match(/[^\d.]+/)[0];
    var bottomPaddingUnit = computedStyles.paddingBottom.match(/[^\d.]+/)[0];
    topPadding = convertToPixels(parseFloat(computedStyles.paddingTop), topPaddingUnit);
    bottomPadding = convertToPixels(parseFloat(computedStyles.paddingBottom), bottomPaddingUnit);
    panelContent.classList.remove("ub-hide");
    panelContent.classList.add("ub-hiding");
    if (toggleContainer.dataset.showonlyone === "true") {
      _toConsumableArray(toggleContainer.children).forEach(function (child) {
        var siblingToggle = child.querySelector(".wp-block-ub-content-toggle-accordion-title-wrap");
        if (siblingToggle !== target) {
          var siblingContent = siblingToggle.nextElementSibling;
          var siblingIndicator = siblingToggle.querySelector(".wp-block-ub-content-toggle-accordion-state-indicator");
          if (!siblingContent.classList.contains("ub-hide")) {
            if (siblingIndicator) siblingIndicator.classList.remove("open");
            siblingContent.classList.add("ub-toggle-transition");
            siblingContent.style.height = "".concat(siblingContent.scrollHeight, "px");
            setTimeout(function () {
              siblingContent.classList.add("ub-hiding");
              siblingContent.style.height = "";
            }, 20);
          }
        }
      });
    }
  } else {
    panelContent.style.height = getComputedStyle(panelContent).height;
  }
  panelContent.classList.add("ub-toggle-transition");
  if (indicator) indicator.classList.toggle("open");
  setTimeout(function () {
    if (panelContent.classList.contains("ub-hiding")) {
      panelContent.style.height = "".concat(panelContent.scrollHeight + topPadding + bottomPadding, "px");
      panelContent.style.paddingTop = "".concat(topPadding, "px");
      panelContent.style.paddingBottom = "".concat(bottomPadding, "px");
      document.querySelectorAll(".ub_image_slider").forEach(function (slider) {
        new Swiper("#".concat(slider.id), JSON.parse(slider.dataset.swiperData));
      });
      setTimeout(function () {
        return window.dispatchEvent(new Event("resize"));
      }, 100);
    } else {
      panelContent.classList.add("ub-hiding");
      panelContent.style.height = "";
    }
  }, 20);
  panelContent.addEventListener("transitionend", function () {
    panelContent.classList.remove("ub-toggle-transition");
    panelContent.setAttribute("aria-expanded", panelContent.offsetHeight !== 0);
    if (panelContent.offsetHeight === 0) {
      panelContent.classList.add("ub-hide");
    } else {
      panelContent.removeAttribute("style");
    }
    panelContent.classList.remove("ub-hiding");
  });
  panelContent.querySelectorAll(".wp-block-embed iframe").forEach(function (embeddedContent) {
    embeddedContent.style.removeProperty("width");
    embeddedContent.style.removeProperty("height");
  });
};
var handleKeyDown = function handleKeyDown(e, i, toggleHeads) {
  var key = e.key;
  if (key === "ArrowUp" && i > 0) {
    e.preventDefault();
    toggleHeads[i - 1].focus();
  } else if (key === "ArrowDown" && i < toggleHeads.length - 1) {
    e.preventDefault();
    toggleHeads[i + 1].focus();
  } else if ([" ", "Enter"].includes(key)) {
    e.preventDefault();
    togglePanel(e.currentTarget);
  } else if (key === "Home" && i > 0) {
    e.preventDefault();
    toggleHeads[0].focus();
  } else if (key === "End" && i < toggleHeads.length - 1) {
    e.preventDefault();
    toggleHeads[toggleHeads.length - 1].focus();
  }
};
var attachTogglePanelEvents = function attachTogglePanelEvents(toggleContainer) {
  var toggleHeads = Array.from(toggleContainer.children).map(function (toggle) {
    return toggle.children[0];
  }).filter(function (toggle) {
    return toggle && toggle.classList.contains("wp-block-ub-content-toggle-accordion-title-wrap");
  });
  toggleHeads.forEach(function (toggleHead, i) {
    toggleHead.removeEventListener("keydown", handleKeyDown);
    toggleHead.addEventListener("keydown", function (e) {
      return handleKeyDown(e, i, toggleHeads);
    });
    toggleHead.removeEventListener("click", togglePanel);
    toggleHead.addEventListener("click", function (e) {
      e.stopImmediatePropagation();
      togglePanel(toggleHead);
    });
  });
};
var initTogglePanels = function initTogglePanels() {
  document.querySelectorAll(".wp-block-ub-content-toggle").forEach(function (toggleContainer) {
    if (window.innerWidth < 700 && JSON.parse(toggleContainer.dataset.mobilecollapse)) {
      _toConsumableArray(toggleContainer.children).forEach(function (child) {
        var panel = child.children[0].nextElementSibling;
        if (!panel.classList.contains("ub-hide")) {
          togglePanel(child.children[0]);
        }
      });
    }
    attachTogglePanelEvents(toggleContainer);
  });
};
document.addEventListener("DOMContentLoaded", function () {
  initTogglePanels();
  var observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      if (mutation.type === "childList") {
        var addedNodes = _toConsumableArray(mutation.addedNodes);
        addedNodes.forEach(function (node) {
          var _node$classList;
          if ((_node$classList = node.classList) !== null && _node$classList !== void 0 && _node$classList.contains("wp-block-ub-content-toggle")) {
            attachTogglePanelEvents(node);
          }
        });
      }
    });
  });
  observer.observe(document.body, {
    childList: true,
    subtree: true
  });
});

/***/ }),

/***/ "./src/blocks/countdown/front.js":
/*!***************************************!*\
  !*** ./src/blocks/countdown/front.js ***!
  \***************************************/
/***/ (() => {

document.addEventListener("DOMContentLoaded", function () {
  var timer = [];
  var initialValue = [];
  var timeUnits = ["week", "day", "hour", "minute", "second"];
  Array.prototype.slice.call(document.getElementsByClassName("ub-countdown")).forEach(function (instance, i) {
    timer[i] = setInterval(function () {
      var timeLeft = parseInt(instance.getAttribute("data-enddate")) - Math.floor(Date.now() / 1000);
      var largestUnit = instance.getAttribute("data-largestunit");
      var smallestUnit = instance.getAttribute("data-smallestunit");
      var seconds = timeLeft % 60;
      var minutes = (timeLeft - seconds) % 3600 / 60;
      var hours = (timeLeft - minutes * 60 - seconds) / 3600;
      if (timeUnits.indexOf(largestUnit) < 2) {
        hours %= 24;
      }
      var days = (timeLeft - hours * 3600 - minutes * 60 - seconds) / 86400;
      if (largestUnit === "week") {
        days %= 7;
      }
      var weeks = (timeLeft - days * 86400 - hours * 3600 - minutes * 60 - seconds) / 604800;
      var animationDirection = "decrease";
      var generateValue = function generateValue(arr) {
        return arr.reduce(function (sum, currDigit, j) {
          return sum + Math.pow(10, arr.length - j - 1) * currDigit;
        }, 0);
      };
      if (!initialValue[i]) {
        //use queryselector only once, then use saved value in future iterations of the loop
        initialValue[i] = Array.prototype.slice.call(instance.querySelectorAll(".ub-countdown-odometer")).map(function (unit) {
          return Array.prototype.slice.call(unit.children).map(function (c) {
            return parseInt(c.innerHTML);
          });
        });
        var conversionFactor = [7, 24, 60, 60, 1];
        if (largestUnit && smallestUnit) {
          var amounts = Array(timeUnits.indexOf(largestUnit)).fill(0).concat(initialValue[i].map(function (arr) {
            return generateValue(arr);
          }), Array(4 - timeUnits.indexOf(smallestUnit)).fill(0));
          if (timeLeft > amounts.reduce(function (total, current, j) {
            return total + current * conversionFactor.slice(j, 4).reduce(function (curFactor, current) {
              return curFactor * current;
            }, 1);
          }, 0)) {
            animationDirection = "increase";
          }
        }
      }
      if (timeLeft >= 0) {
        if (instance.querySelector(".ub-countdown-odometer-container")) {
          var breakIntoDigits = function breakIntoDigits(num, minDigits) {
            //adapted from from https://stackoverflow.com/a/7784664
            var digits = [];
            while (num > 0) {
              digits.push(num % 10);
              num = parseInt(num / 10);
            }
            var missingDigits = minDigits - digits.length;
            return (missingDigits > 0 ? Array(missingDigits).fill(0) : []).concat(digits.reverse());
          };
          var integerArray = function integerArray(limit1, limit2) {
            if (limit1 === limit2) {
              return [limit1];
            } else if (limit1 < limit2) {
              return Array.apply(null, Array(limit2 - limit1 + 1)).map(function (_, i) {
                return i;
              }).map(function (a) {
                return a + limit1;
              });
            } else {
              return Array.apply(null, Array(limit1 - limit2 + 1)).map(function (_, i) {
                return i;
              }).map(function (a) {
                return limit1 - a;
              });
            }
          };

          //make array of max values
          var _timeUnits = ["week", "day", "hour", "minute", "second"];
          var maxValues = [0, 6, 23, 59, 59].slice(_timeUnits.indexOf(largestUnit), _timeUnits.indexOf(smallestUnit) + 1);
          if (_timeUnits.indexOf(largestUnit) < 3) {
            maxValues[0] = 0;
          }
          var replacements = [weeks, days, hours, minutes, seconds].slice(_timeUnits.indexOf(largestUnit), _timeUnits.indexOf(smallestUnit) + 1).map(function (r, j) {
            return breakIntoDigits(r, Math.floor(Math.log10(maxValues[j] || Math.max(generateValue(initialValue[i][j]), r) || 1) + 1));
          });
          var incomingDigits = []; //should also contain digits for other values

          initialValue[i].forEach(function (display, j) {
            if (display.every(function (digit, k) {
              return digit === replacements[j][k];
            })) {
              incomingDigits.push(display);
            } else {
              var currentValue = generateValue(display);
              var newValue = generateValue(replacements[j]);
              var digitCount = maxValues[j] ? Math.floor(Math.log10(maxValues[j])) + 1 : currentValue === 0 && newValue === 0 ? 1 : Math.floor(Math.log10(Math.max(currentValue, newValue))) + 1;
              var addExtraZeroes = function addExtraZeroes(arr, targetLength) {
                return [].concat(Array(targetLength - arr.length).fill(0), arr);
              };
              if (display.length < digitCount) {
                addExtraZeroes(display, digitCount);
              }
              if (replacements[j].count < digitCount) {
                addExtraZeroes(replacements[j], digitCount);
              }
              if (animationDirection === "increase") {
                var maxDigits = breakIntoDigits(maxValues[j] || newValue);
                if (maxDigits.length === 0) {
                  maxDigits = [0];
                }
                var extraDigits = [];
                var prevDigits = [];
                incomingDigits.push(replacements[j].map(function (d, k) {
                  var currentMax = display[k - 1] === maxDigits[k - 1] ? maxDigits[i] : 9;
                  if (prevDigits.length > 1) {
                    var prevDigits2 = prevDigits.slice(1, prevDigits.length - 1);
                    var cycle = prevDigits2.map(function (p) {
                      return integerArray(0, maxDigits[k - 1] === p ? maxDigits[k] : 9);
                    });
                    extraDigits = cycle.reduce(function (prev, curr) {
                      return prev.concat(curr);
                    }, []);
                  }
                  if (d === display[k]) {
                    if (newValue > currentValue) {
                      prevDigits = prevDigits.length > 0 ? integerArray(d, currentMax).concat(extraDigits, integerArray(0, d)) : [d];
                    } else {
                      prevDigits = extraDigits.concat(integerArray(0, d));
                    }
                  } else if (display[k] < d) {
                    if (prevDigits.length > 1) {
                      prevDigits = integerArray(display[k], currentMax).concat(extraDigits, integerArray(0, d));
                    } else {
                      prevDigits = integerArray(display[k], d);
                    }
                  } else {
                    prevDigits = integerArray(display[k], currentMax).concat(extraDigits, integerArray(0, d));
                  }
                  return prevDigits.length > 1 ? prevDigits : d;
                }));
              } else if (animationDirection === "decrease") {
                var _maxDigits = breakIntoDigits(maxValues[j] || currentValue);
                if (_maxDigits.length === 0) {
                  _maxDigits = [0];
                }
                var _extraDigits = [];
                var _prevDigits = [];
                incomingDigits.push(replacements[j].map(function (d, k) {
                  var currentMax = replacements[j][k - 1] === _maxDigits[k - 1] ? _maxDigits[k] : 9;
                  if (_prevDigits.length > 1) {
                    var prevDigits2 = _prevDigits.slice(1, _prevDigits.length - 1);
                    var cycle = prevDigits2.map(function (p) {
                      return integerArray(0, _maxDigits[k - 1] === p ? _maxDigits[k] : 9);
                    });
                    _extraDigits = cycle.reduce(function (prev, curr) {
                      return prev.concat(curr);
                    }, []);
                  }
                  if (d === display[k]) {
                    if (newValue < currentValue) {
                      _prevDigits = _prevDigits.length > 0 ? integerArray(d, currentMax).concat(_extraDigits, integerArray(0, d)) : [d];
                    } else {
                      _prevDigits = integerArray(d, currentMax).concat(_extraDigits, integerArray(0, d));
                    }
                  } else if (display[k] > d) {
                    if (_prevDigits.length > 1) {
                      _prevDigits = integerArray(d, currentMax).concat(_extraDigits, integerArray(0, display[k]));
                    } else {
                      _prevDigits = integerArray(d, display[k]);
                    }
                  } else {
                    _prevDigits = integerArray(d, currentMax).concat(_extraDigits, integerArray(0, display[k]));
                  }
                  return _prevDigits.length > 1 ? _prevDigits : d;
                }));
              }
            }
          });
          var odometerSlot = Array.prototype.slice.call(instance.querySelectorAll(".ub-countdown-odometer")).map(function (a) {
            return Array.prototype.slice.call(a.children);
          });
          var finishedTransitions = 0;
          var transitionCount = incomingDigits.reduce(function (collection, currentArray) {
            return collection.concat(currentArray);
          }, []).filter(function (a) {
            return Array.isArray(a);
          }).length;
          var removeExtraZeroes = function removeExtraZeroes() {
            maxValues.forEach(function (m, j) {
              if (m === 0) {
                //at least one extra leading zero is spotted
                if (initialValue[i][j][0] === 0) {
                  var curVal = generateValue(initialValue[i][j]);
                  var targetLength = (curVal ? Math.floor(Math.log10(curVal)) : 0) + 1;

                  //eliminate element containing extra zero

                  odometerSlot[j].slice(0, initialValue[i][j].length - targetLength).forEach(function (o) {
                    o.parentNode.removeChild(o);
                  });
                  initialValue[i][j] = initialValue[i][j].slice(initialValue[i][j].length - targetLength);
                }
              }
            });
          };
          incomingDigits.forEach(function (arr, j) {
            arr.forEach(function (d, k) {
              if (Array.isArray(d)) {
                odometerSlot[j][k].classList.add("ub-countdown-odometer-digits");
                odometerSlot[j][k].classList.remove("ub-countdown-odometer-digit");
                odometerSlot[j][k].innerHTML = d.map(function (dd) {
                  return "<div class=\"odometer-moving-digit\">".concat(dd, "</div>");
                }).join("");

                //do pre-animation prep
                if (animationDirection === "decrease") {
                  odometerSlot[j][k].style.transform = "translateY(".concat(100 * (1 / d.length - 1), "%)");
                }

                //animate
                setTimeout(function () {
                  odometerSlot[j][k].style.transition = "all 0.3s";
                  if (animationDirection === "increase") {
                    odometerSlot[j][k].style.transform = "translateY(".concat(100 * (1 / d.length - 1), "%)");
                  } else {
                    odometerSlot[j][k].style.transform = "translateY(0px)";
                  }
                  //event listener for end of animation
                  odometerSlot[j][k].addEventListener("transitionend", function () {
                    //switch to pre-animation style
                    odometerSlot[j][k].classList.add("ub-countdown-odometer-digit");
                    odometerSlot[j][k].classList.remove("ub-countdown-odometer-digits");
                    odometerSlot[j][k].removeAttribute("style");

                    //check if there are leading zeroes to be removed
                    odometerSlot[j][k].innerHTML = replacements[j][k];
                    initialValue[i][j][k] = replacements[j][k];
                    finishedTransitions++;
                    if (finishedTransitions === transitionCount) {
                      removeExtraZeroes();
                    }
                  }, {
                    once: true
                  });
                }, 40);
              }
            });
          });
          animationDirection = "decrease";
        } else {
          if (instance.querySelector(".ub_countdown_week")) instance.querySelector(".ub_countdown_week").innerHTML = weeks;
          if (instance.querySelector(".ub_countdown_day")) instance.querySelector(".ub_countdown_day").innerHTML = days;
          if (instance.querySelector(".ub_countdown_hour")) instance.querySelector(".ub_countdown_hour").innerHTML = hours;
          if (instance.querySelector(".ub_countdown_minute")) instance.querySelector(".ub_countdown_minute").innerHTML = minutes;
          if (instance.querySelector(".ub_countdown_second")) instance.querySelector(".ub_countdown_second").innerHTML = seconds;
          if (instance.querySelector(".ub_countdown_circular_container")) {
            if (instance.querySelector(".ub_countdown_circle_week")) {
              instance.querySelector(".ub_countdown_circle_week .ub_countdown_circle_path").style.strokeDasharray = "".concat(weeks * 219.911 / 52, "px, 219.911px");
              instance.querySelector(".ub_countdown_circle_week .ub_countdown_circle_trail").style.strokeLinecap = weeks > 0 ? "round" : "butt";
            }
            if (instance.querySelector(".ub_countdown_circle_day")) {
              instance.querySelector(".ub_countdown_circle_day .ub_countdown_circle_path").style.strokeDasharray = "".concat(days * 219.911 / 7, "px, 219.911px");
              instance.querySelector(".ub_countdown_circle_day .ub_countdown_circle_trail").style.strokeLinecap = days > 0 ? "round" : "butt";
            }
            if (instance.querySelector(".ub_countdown_circle_hour")) {
              instance.querySelector(".ub_countdown_circle_hour .ub_countdown_circle_path").style.strokeDasharray = "".concat(hours * 219.911 / 24, "px, 219.911px");
              instance.querySelector(".ub_countdown_circle_hour .ub_countdown_circle_trail").style.strokeLinecap = hours > 0 ? "round" : "butt";
            }
            if (instance.querySelector(".ub_countdown_circle_minute")) {
              instance.querySelector(".ub_countdown_circle_minute .ub_countdown_circle_path").style.strokeDasharray = "".concat(minutes * 219.911 / 60, "px, 219.911px");
              instance.querySelector(".ub_countdown_circle_minute .ub_countdown_circle_trail").style.strokeLinecap = minutes > 0 ? "round" : "butt";
            }
            if (instance.querySelector(".ub_countdown_circle_second")) {
              instance.querySelector(".ub_countdown_circle_second .ub_countdown_circle_path").style.strokeDasharray = "".concat(seconds * 219.911 / 60, "px, 219.911px");
              instance.querySelector(".ub_countdown_circle_second .ub_countdown_circle_trail").style.strokeLinecap = seconds > 0 ? "round" : "butt";
            }
          }
        }
      } else {
        clearInterval(timer[i]);
        if (!isNaN(timeLeft)) {
          instance.innerText = instance.getAttribute("data-expirymessage");
        }
      }
    }, 1000);
  });
});

/***/ }),

/***/ "./src/blocks/counter/front.js":
/*!*************************************!*\
  !*** ./src/blocks/counter/front.js ***!
  \*************************************/
/***/ (() => {

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var UltimateBlocksCounter = /*#__PURE__*/function () {
  function UltimateBlocksCounter(wrapper) {
    _classCallCheck(this, UltimateBlocksCounter);
    this.container = wrapper;
    this.counterNumber = this.container.querySelector(".ub_counter-number");
    this.startCount = parseInt(this.container.dataset.start_num, 10);
    this.stopCounter = parseInt(this.container.dataset.end_num, 10);
    this.animationDuration = parseInt(this.container.dataset.animation_duration, 10);
    this.frameDuration = 1000 / 60;
    this.totalFrames = Math.round(this.animationDuration * 1000 / this.frameDuration);
    this.easeOutQuad = function (t) {
      return t * (2 - t);
    };
  }
  return _createClass(UltimateBlocksCounter, [{
    key: "initialize",
    value: function initialize() {
      this.updateCounter();
    }
  }, {
    key: "updateCounter",
    value: function updateCounter() {
      var _this = this;
      var frame = 0;
      var countTo = this.stopCounter - this.startCount;
      var interval = setInterval(function () {
        frame++;
        var progress = _this.easeOutQuad(frame / _this.totalFrames);
        var currentCount = Math.round(countTo * progress) + _this.startCount;
        if (parseInt(_this.counterNumber.innerHTML, 10) !== currentCount) {
          _this.counterNumber.innerHTML = currentCount;
        }
        if (frame === _this.totalFrames) {
          clearInterval(interval);
        }
      }, this.frameDuration);
    }
  }]);
}();
window.addEventListener("DOMContentLoaded", function () {
  var container = document.querySelectorAll(".ub_counter");
  var observerOptions = {
    root: null,
    rootMargin: "0px",
    threshold: 0.1
  };
  var observer = new IntersectionObserver(function (entries, observer) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        var wrapper = entry.target;
        new UltimateBlocksCounter(wrapper).initialize();
        observer.unobserve(wrapper); // Unobserve after initializing to avoid re-triggering
      }
    });
  }, observerOptions);
  container.forEach(function (wrapper) {
    return observer.observe(wrapper);
  });
});

/***/ }),

/***/ "./src/blocks/expand/front.js":
/*!************************************!*\
  !*** ./src/blocks/expand/front.js ***!
  \************************************/
/***/ (() => {

if (!Element.prototype.matches) {
  Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}
if (!Element.prototype.closest) {
  Element.prototype.closest = function (s) {
    var el = this;
    do {
      if (el.matches(s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);
    return null;
  };
}
function ub_getSiblings(element, criteria) {
  var children = Array.prototype.slice.call(element.parentNode.children).filter(function (child) {
    return child !== element;
  });
  return criteria ? children.filter(criteria) : children;
}
Array.prototype.slice.call(document.getElementsByClassName("ub-expand-toggle-button")).forEach(function (instance) {
  if (instance.getAttribute("aria-controls") === "") {
    var blockID = instance.parentElement.parentElement.id.slice(10);
    instance.setAttribute("aria-controls", "ub-expand-full-".concat(blockID));
    if (instance.parentElement.classList.contains("ub-expand-full")) {
      instance.parentElement.setAttribute("id", "ub-expand-full-".concat(blockID));
    }
  }
  var togglePanel = function togglePanel(e) {
    var blockRoot = instance.closest(".ub-expand");
    blockRoot.querySelector(".ub-expand-partial .ub-expand-toggle-button").classList.toggle("ub-hide");
    var expandingPart = Array.prototype.slice.call(blockRoot.children).filter(function (child) {
      return child.classList.contains("ub-expand-full");
    })[0];
    expandingPart.classList.toggle("ub-hide");
    if (expandingPart.classList.contains("ub-hide")) {
      var expandRoot = e.target.parentElement.parentElement;
      var rootPosition = expandRoot.getBoundingClientRect().top;
      var expandScrollData = expandRoot.dataset;
      if (rootPosition < 0) {
        if ("scrollType" in expandScrollData) {
          var scrollType = expandScrollData.scrollType;
          var offset = scrollType === "fixedamount" ? expandScrollData.scrollAmount : 0;
          var target = scrollType === "namedelement" ? expandScrollData.scrollTarget : "";
          switch (scrollType) {
            case "auto":
              var probableHeaders;
              try {
                probableHeaders = document.elementsFromPoint(window.innerWidth / 2, 0);
              } catch (e) {
                probableHeaders = document.msElementsFromPoint(window.innerWidth / 2, 0);
              }
              var stickyHeaders = Array.prototype.slice.call(probableHeaders).filter(function (e) {
                return ["fixed", "sticky"].includes(window.getComputedStyle(e).position);
              });
              var stickyHeaderHeights = stickyHeaders.map(function (h) {
                return h.offsetHeight;
              });
              window.scrollBy(0, rootPosition - (stickyHeaders.length ? Math.max.apply(Math, stickyHeaderHeights) : 0));
              break;
            case "fixedamount":
              window.scrollBy(0, rootPosition - offset);
              break;
            case "namedelement":
              window.scrollBy(0, rootPosition - (document.querySelector(target) ? document.querySelector(target).offsetHeight : 0));
              break;
            default:
              window.scrollBy(0, rootPosition);
          }
        } else {
          window.scrollBy(0, expandRoot.getBoundingClientRect().rootPosition);
        }
      }
    } else {
      Array.prototype.slice.call(document.getElementsByClassName("ub_image_slider")).forEach(function (slider) {
        var swiper = new Swiper("#".concat(slider.id), JSON.parse(slider.dataset.swiperData));
      });
      setTimeout(function () {
        window.dispatchEvent(new Event("resize"));
      }, 100);
    }
    Array.prototype.slice.call(expandingPart.querySelectorAll(".wp-block-embed iframe")).forEach(function (embeddedContent) {
      embeddedContent.style.removeProperty("width");
      embeddedContent.style.removeProperty("height");
    });
  };
  instance.addEventListener("click", togglePanel);
  instance.addEventListener("keydown", function (e) {
    if ([" ", "Enter"].indexOf(e.key) > -1) {
      e.preventDefault();
      togglePanel();
      Array.prototype.slice.call(instance.parentElement.parentElement.children).filter(function (a) {
        return a !== instance.parentElement;
      })[0].querySelector("[aria-controls=\"".concat(instance.getAttribute("aria-controls"), "\"]")).focus();
    }
  });
});

/***/ }),

/***/ "./src/blocks/image-slider/front.js":
/*!******************************************!*\
  !*** ./src/blocks/image-slider/front.js ***!
  \******************************************/
/***/ (() => {

Array.prototype.slice.call(document.getElementsByClassName("ub_image_slider")).forEach(function (instance) {
  var _instance$getElements, _instance$getElements2;
  var swiper = new Swiper("#".concat(instance.id), JSON.parse(instance.dataset.swiperData));
  instance === null || instance === void 0 || (_instance$getElements = instance.getElementsByClassName("swiper-button-next")[0]) === null || _instance$getElements === void 0 || _instance$getElements.addEventListener("keydown", function (e) {
    if (e.key === " ") {
      e.preventDefault();
    }
  });
  instance === null || instance === void 0 || (_instance$getElements2 = instance.getElementsByClassName("swiper-button-prev")[0]) === null || _instance$getElements2 === void 0 || _instance$getElements2.addEventListener("keydown", function (e) {
    if (e.key === " ") {
      e.preventDefault();
    }
  });
  Array.prototype.slice.call(instance.getElementsByClassName("swiper-pagination-bullet")).forEach(function (bullet) {
    bullet.addEventListener("keydown", function (e) {
      if (e.key === " ") {
        e.preventDefault();
      }
    });
  });
});

/***/ }),

/***/ "./src/blocks/progress-bar/front.js":
/*!******************************************!*\
  !*** ./src/blocks/progress-bar/front.js ***!
  \******************************************/
/***/ (() => {

document.addEventListener("DOMContentLoaded", function () {
  setTimeout(function () {
    Array.prototype.slice.call(document.getElementsByClassName("ub_progress-bar")).forEach(function (instance) {
      instance.classList.add("ub_progress-bar-filled");
    });
  }, 500);
});

/***/ }),

/***/ "./src/blocks/tabbed-content/front.js":
/*!********************************************!*\
  !*** ./src/blocks/tabbed-content/front.js ***!
  \********************************************/
/***/ (() => {

/* eslint-disable */

if (!Element.prototype.matches) {
  Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}
if (!Element.prototype.closest) {
  Element.prototype.closest = function (s) {
    var el = this;
    do {
      if (el.matches(s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);
    return null;
  };
}
function ub_getSiblings(element, criteria) {
  var children = Array.prototype.slice.call(element.parentNode.children).filter(function (child) {
    return child !== element;
  });
  return criteria ? children.filter(criteria) : children;
}
function ub_getNodeindex(elem) {
  return Array.prototype.slice.call(elem.parentNode.children).indexOf(elem);
}
function ub_handleTabEvent(tab) {
  var parent = tab.closest(".wp-block-ub-tabbed-content-holder");
  var isVertical = parent.classList.contains("vertical-holder");
  var activeStyle = parent.querySelector(".wp-block-ub-tabbed-content-tab-title-".concat(isVertical ? "vertical-" : "", "wrap.active")).getAttribute("style");
  var defaultStyle = parent.querySelector(".wp-block-ub-tabbed-content-tab-title-".concat(isVertical ? "vertical-" : "", "wrap:not(.active)")).getAttribute("style");
  ub_getSiblings(tab, function (elem) {
    return elem.classList.contains("wp-block-ub-tabbed-content-tab-title-".concat(isVertical ? "vertical-" : "", "wrap"));
  }).forEach(function (sibling) {
    if (sibling.tabIndex === 0) {
      sibling.setAttribute("tabindex", -1);
    }
    sibling.classList.remove("active");
    sibling.setAttribute("aria-selected", false);
    if (defaultStyle) {
      sibling.setAttribute("style", defaultStyle);
    }
  });
  tab.setAttribute("aria-selected", true);
  tab.classList.add("active");
  parent.dataset.activeTabs = JSON.stringify([parseInt(tab.id.match(/-\d+$/g)[0].slice(1))]);
  tab.setAttribute("tabindex", 0);
  var _tab$parentElement$ge = tab.parentElement.getBoundingClientRect(),
    tabContainerWidth = _tab$parentElement$ge.width;
  var tabContainerLocation = tab.parentElement.getBoundingClientRect().x || tab.parentElement.getBoundingClientRect().left;
  var _tab$getBoundingClien = tab.getBoundingClientRect(),
    tabWidth = _tab$getBoundingClien.width;
  var tabLocation = tab.getBoundingClientRect().x || tab.getBoundingClientRect().left;
  if (tabContainerLocation > tabLocation) {
    tab.parentElement.scrollLeft -= tabContainerLocation - tabLocation;
  }
  if (tabLocation + tabWidth > tabContainerLocation + tabContainerWidth) {
    var scrollLeftChange = tabLocation - tabContainerLocation;
    if (tabWidth <= tabContainerWidth) {
      scrollLeftChange += tabWidth - tabContainerWidth;
    }
    tab.parentElement.scrollLeft += scrollLeftChange;
  }
  if (activeStyle) tab.setAttribute("style", activeStyle);
  var tabContentContainer = Array.prototype.slice.call(parent.children).filter(function (elem) {
    return elem.classList.contains("wp-block-ub-tabbed-content-tabs-content");
  })[0];
  Array.prototype.slice.call(tabContentContainer.children).filter(function (child) {
    return child.classList.contains("wp-block-ub-tabbed-content-tab-content-wrap");
  }).forEach(function (tabContent, i) {
    if (ub_getNodeindex(tab) === i) {
      tabContent.classList.add("active");
      tabContent.classList.remove("ub-hide");
      Array.prototype.slice.call(document.getElementsByClassName("ub_image_slider")).forEach(function (slider) {
        var swiper = new Swiper("#".concat(slider.id), JSON.parse(slider.dataset.swiperData));
      });
      setTimeout(function () {
        window.dispatchEvent(new Event("resize"));
      }, 100);
      Array.prototype.slice.call(tabContent.querySelectorAll(".wp-block-embed iframe")).forEach(function (embeddedContent) {
        embeddedContent.style.removeProperty("width");
        embeddedContent.style.removeProperty("height");
      });
      if (tabContent.dataset.tabAnchor) {
        history.replaceState({
          page: history.length
        }, document.getElementsByTagName("title")[0].innerHTML, "#".concat(tabContent.dataset.tabAnchor)); //doesn't trigger window.onhashchange
      }
    } else {
      tabContent.classList.remove("active");
      tabContent.classList.add("ub-hide");
    }
  });
  Array.prototype.slice.call(tabContentContainer.children).filter(function (child) {
    return child.classList.contains("wp-block-ub-tabbed-content-accordion-toggle");
  }).forEach(function (accordionToggle) {
    if (ub_getNodeindex(accordionToggle) / 2 === ub_getNodeindex(tab)) {
      accordionToggle.classList.add("active");
    } else {
      accordionToggle.classList.remove("active");
    }
  });
}
function ub_checkPrevTab(event) {
  event.preventDefault();
  if (event.target.previousElementSibling) {
    event.target.setAttribute("tabindex", -1);
    event.target.previousElementSibling.focus();
  } else {
    ub_focusOnLastTab(event);
  }
}
function ub_checkNextTab(event) {
  event.preventDefault();
  if (event.target.nextElementSibling) {
    event.target.setAttribute("tabindex", -1);
    event.target.nextElementSibling.focus();
  } else {
    ub_focusOnFirstTab(event);
  }
}
function ub_focusOnFirstTab(event) {
  event.preventDefault();
  event.target.setAttribute("tabindex", -1);
  event.target.parentElement.children[0].focus();
}
function ub_focusOnLastTab(event) {
  event.preventDefault();
  event.target.setAttribute("tabindex", -1);
  var tabs = event.target.parentElement.children;
  tabs[tabs.length - 1].focus();
}
function ub_commonKeyPress(event) {
  if (event.key === "Home") {
    ub_focusOnFirstTab(event);
  } else if (event.key === "End") {
    ub_focusOnLastTab(event);
  }
}
function ub_upDownPress(event) {
  if (event.key === "ArrowUp") {
    ub_checkPrevTab(event);
  } else if (event.key === "ArrowDown") {
    ub_checkNextTab(event);
  } else {
    ub_commonKeyPress(event);
  }
}
function ub_leftRightPress(event) {
  if (event.key === "ArrowLeft") {
    ub_checkPrevTab(event);
  } else if (event.key === "ArrowRight") {
    ub_checkNextTab(event);
  } else {
    ub_commonKeyPress(event);
  }
}
Array.prototype.slice.call(document.getElementsByClassName("wp-block-ub-tabbed-content-tab-title-wrap")).forEach(function (instance) {
  instance.addEventListener("focus", function () {
    ub_handleTabEvent(instance);
  });
  var tabBar = instance.parentElement;
  var tabBarIsBeingDragged = false;
  var oldScrollPosition = -1;
  var oldMousePosition = -1;
  var tabBarLocation = tabBar.getBoundingClientRect().x || tabBar.getBoundingClientRect().left;
  tabBar.addEventListener("mousedown", function (e) {
    tabBarIsBeingDragged = true;
    oldScrollPosition = tabBar.scrollLeft;
    oldMousePosition = e.clientX - tabBarLocation;
  });
  document.addEventListener("mouseup", function () {
    if (tabBarIsBeingDragged) {
      tabBarIsBeingDragged = false;
      oldScrollPosition = -1;
      oldMousePosition = -1;
    }
  });
  document.addEventListener("mousemove", function (e) {
    if (tabBarIsBeingDragged) {
      e.preventDefault();
      var newMousePosition = e.clientX - tabBarLocation;
      tabBar.scrollLeft = oldScrollPosition - newMousePosition + oldMousePosition;
    }
  });
});
Array.prototype.slice.call(document.getElementsByClassName("wp-block-ub-tabbed-content-tab-title-vertical-wrap")).forEach(function (instance) {
  instance.addEventListener("focus", function () {
    ub_handleTabEvent(instance);
  });
});
function ub_switchFocusToTab(index, tabBar) {
  var _tabBar$getBoundingCl = tabBar.getBoundingClientRect(),
    tabContainerWidth = _tabBar$getBoundingCl.width;
  var tabContainerLocation = tabBar.getBoundingClientRect().x || tabBar.getBoundingClientRect().left;
  var _tabBar$children$inde = tabBar.children[index].getBoundingClientRect(),
    tabWidth = _tabBar$children$inde.width;
  var tabLocation = tabBar.children[index].getBoundingClientRect().x || tabBar.children[index].getBoundingClientRect().left;
  if (tabContainerLocation > tabLocation) {
    tabBar.scrollLeft -= tabContainerLocation - tabLocation;
  }
  if (tabLocation + tabWidth > tabContainerLocation + tabContainerWidth) {
    var scrollLeftChange = tabLocation - tabContainerLocation;
    if (tabWidth <= tabContainerWidth) {
      scrollLeftChange += tabWidth - tabContainerWidth;
    }
    tabBar.scrollLeft += scrollLeftChange;
  }
}
function ub_getTabbedContentDisplayModes(block) {
  var dm = [];
  var classNamePrefix = "wp-block-ub-tabbed-content";
  if (block.classList.contains("".concat(classNamePrefix, "-vertical-holder-mobile"))) {
    dm.push("verticaltab");
  } else if (block.classList.contains("".concat(classNamePrefix, "-horizontal-holder-mobile"))) {
    dm.push("horizontaltab");
  } else {
    dm.push("accordion");
  }
  if (block.classList.contains("".concat(classNamePrefix, "-vertical-holder-tablet"))) {
    dm.push("verticaltab");
  } else if (block.classList.contains("".concat(classNamePrefix, "-horizontal-holder-tablet"))) {
    dm.push("horizontaltab");
  } else {
    dm.push("accordion");
  }
  dm.push(block.classList.contains("vertical-holder") ? "verticaltab" : "horizontaltab");
  return dm;
}
(function () {
  var displayModes = Array.prototype.slice.call(document.getElementsByClassName("wp-block-ub-tabbed-content")).map(function (block) {
    return ub_getTabbedContentDisplayModes(block);
  });
  var transitionTo = 0;
  var transitionFrom = 0;
  function processTransition() {
    if (transitionTo && transitionFrom) {
      Array.prototype.slice.call(document.getElementsByClassName("wp-block-ub-tabbed-content")).forEach(function (instance, i) {
        if (displayModes[i][transitionFrom - 1] !== displayModes[i][transitionTo - 1]) {
          var tabBar = instance.children[0].children[0];
          switch (displayModes[i][transitionFrom - 1]) {
            case "accordion":
              var activeTabs = JSON.parse(instance.dataset.activeTabs);
              if (activeTabs) {
                Array.prototype.slice.call(tabBar.children).forEach(function (child, j) {
                  if (j === activeTabs[activeTabs.length - 1]) {
                    child.classList.add("active");
                  } else {
                    child.classList.remove("active");
                  }
                });
                Array.prototype.slice.call(instance.children[1].children).forEach(function (child, j) {
                  if (Math.floor(j / 2) === activeTabs[activeTabs.length - 1]) {
                    child.classList.add("active");
                    child.classList.remove("ub-hide");
                  } else {
                    child.classList.remove("active");
                    if (j % 2 === 1) {
                      child.classList.add("ub-hide");
                    }
                  }
                });
              }
              delete instance.dataset.activeTabs;
              Array.prototype.slice.call(instance.children[1]).forEach(function (child, j) {
                if (j % 2 === 1) {
                  child.setAttribute("role", "tabpanel");
                  child.setAttribute("aria-labelledby", child.id.replace("panel", "tab"));
                }
              });
              break;
            case "verticaltab":
              Array.prototype.slice.call(tabBar.children).forEach(function (tab) {
                tab.removeEventListener("keydown", ub_upDownPress);
              });
              break;
            case "horizontaltab":
            default:
              Array.prototype.slice.call(tabBar.children).forEach(function (tab) {
                tab.removeEventListener("keydown", ub_leftRightPress);
              });
              break;
          }
          switch (displayModes[i][transitionTo - 1]) {
            case "accordion":
              Array.prototype.slice.call(tabBar.children).forEach(function (child, j) {
                if (child.classList.contains("active")) {
                  instance.dataset.activeTabs = JSON.stringify([j]);
                }
              });
              Array.prototype.slice.call(instance.children[1]).forEach(function (child, j) {
                if (j % 2 === 1) {
                  child.setAttribute("role", "region");
                  child.removeAttribute("aria-labelledby");
                } else {
                  child.setAttribute("aria-expanded", !child.nextElementSibling.classList.contains("ub-hide"));
                  if (!child.hasAttribute("aria-controls")) {
                    child.setAttribute("aria-controls", child.nextElementSibling.id);
                  }
                }
              });
              tabBar.removeAttribute("aria-orientation");
              break;
            case "verticaltab":
              Array.prototype.slice.call(tabBar.children).forEach(function (tab) {
                tab.addEventListener("keydown", ub_upDownPress);
              });
              tabBar.setAttribute("aria-orientation", "vertical");
              break;
            case "horizontaltab":
            default:
              var newActiveTab = Array.prototype.slice.call(tabBar.children).findIndex(function (tab) {
                return tab.classList.contains("active");
              });
              ub_switchFocusToTab(newActiveTab, tabBar);
              Array.prototype.slice.call(tabBar.children).forEach(function (tab) {
                tab.addEventListener("keydown", ub_leftRightPress);
              });
              tabBar.setAttribute("aria-orientation", "horizontal");
              break;
          }
        }
      });
      transitionTo = 0;
      transitionFrom = 0;
    }
  }

  //Keep addListener for these three. addEventListener won't work with safari versions older than 14.

  window.matchMedia("(max-width: 699px)").addEventListener("change", function (mql) {
    if (mql.matches) {
      transitionTo = 1;
    } else {
      transitionFrom = 1;
    }
    processTransition();
  });
  window.matchMedia("(min-width: 700px) and (max-width: 899px)").addEventListener("change", function (mql) {
    if (mql.matches) {
      transitionTo = 2;
    } else {
      transitionFrom = 2;
    }
    processTransition();
  });
  window.matchMedia("(min-width: 900px)").addEventListener("change", function (mql) {
    if (mql.matches) {
      transitionTo = 3;
    } else {
      transitionFrom = 3;
    }
    processTransition();
  });
})();
function ub_hashTabSwitch() {
  var targetElement = document.querySelector("[data-tab-anchor='".concat(window.location.hash.slice(1), "']"));
  var classNamePrefix = "wp-block-ub-tabbed-content";
  if (targetElement) {
    if (targetElement.classList.contains("".concat(classNamePrefix, "-tab-content-wrap"))) {
      var probableHeaders;
      try {
        probableHeaders = document.elementsFromPoint(window.innerWidth / 2, 0);
      } catch (e) {
        probableHeaders = document.msElementsFromPoint(window.innerWidth / 2, 0);
      }
      var stickyHeaders = Array.prototype.slice.call(probableHeaders).filter(function (e) {
        return ["fixed", "sticky"].includes(window.getComputedStyle(e).position);
      });
      var stickyHeaderHeights = stickyHeaders.map(function (h) {
        return h.offsetHeight;
      });
      var tabIndex = Array.prototype.slice.call(targetElement.parentElement.children).filter(function (e) {
        return e.classList.contains("".concat(classNamePrefix, "-tab-content-wrap"));
      }).findIndex(function (e) {
        return e.dataset.tabAnchor === window.location.hash.slice(1);
      });
      var tabContentRoot = targetElement.parentElement.parentElement;
      var ancestorTabBlocks = [];
      var ancestorTabIndexes = [];
      var _findTabContentRoot = function findTabContentRoot(currentBlock) {
        var parentTabbedContent = currentBlock.closest(".wp-block-ub-tabbed-content-tabs-content");
        if (parentTabbedContent) {
          ancestorTabIndexes.push(Array.prototype.slice.call(parentTabbedContent.children).filter(function (t) {
            return t.classList.contains("wp-block-ub-tabbed-content-tab-content-wrap");
          }).indexOf(currentBlock.closest(".wp-block-ub-tabbed-content-tab-content-wrap")));
          ancestorTabBlocks.push(parentTabbedContent.parentElement);
          return _findTabContentRoot(parentTabbedContent.parentElement);
        } else {
          return currentBlock;
        }
      };
      _findTabContentRoot(targetElement);
      ancestorTabBlocks.unshift(targetElement.parentElement.parentElement);
      ancestorTabIndexes.unshift(tabIndex);
      var displayModes = ancestorTabBlocks.map(function (a) {
        return ub_getTabbedContentDisplayModes(a);
      });
      var displayMode = -1;
      if (window.innerWidth < 700) {
        displayMode = 0;
      } else if (window.innerWidth < 900) {
        displayMode = 1;
      } else {
        displayMode = 2;
      }
      ancestorTabBlocks.forEach(function (a, i) {
        var targetElement = a.children[1].children[ancestorTabIndexes[i]];
        var tabContents = Array.prototype.slice.call(targetElement.parentElement.children).filter(function (e) {
          return e.classList.contains("".concat(classNamePrefix, "-tab-content-wrap"));
        });
        if (displayModes[i][displayMode] === "accordion") {
          tabContents.forEach(function (tabContent, j) {
            if (j === ancestorTabIndexes[i]) {
              tabContent.classList.add("active");
              tabContent.classList.remove("ub-hide");
              tabContent.previousElementSibling.classList.add("active");
            } else {
              tabContent.classList.remove("active");
              tabContent.classList.add("ub-hide");
              tabContent.previousElementSibling.classList.remove("active");
            }
          });
          targetElement.parentElement.parentElement.dataset.activeTabs = JSON.stringify([ancestorTabIndexes[i]]);
        } else {
          var tabBar = targetElement.parentElement.previousElementSibling.children[0];
          Array.prototype.slice.call(tabBar.children).forEach(function (tab, j) {
            var probableAccordionToggle = tabContents[ancestorTabIndexes[i]].previousElementSibling;
            tab.setAttribute("aria-selected", j === ancestorTabIndexes[i]);
            if (j === ancestorTabIndexes[i]) {
              tab.classList.add("active");
              tabContents[j].classList.add("active");
              tabContents[j].classList.remove("ub-hide");
              if (probableAccordionToggle && probableAccordionToggle.classList.contains("".concat(classNamePrefix, "-accordion-toggle"))) {
                probableAccordionToggle.classList.add("active");
              }
              Array.prototype.slice.call(tabContents[j].querySelectorAll("iframe")).forEach(function (embed) {
                embed.style.removeProperty("width");
                embed.style.removeProperty("height");
              });
            } else {
              tab.classList.remove("active");
              tabContents[j].classList.remove("active");
              tabContents[j].classList.add("ub-hide");
              if (probableAccordionToggle && probableAccordionToggle.classList.contains("".concat(classNamePrefix, "-accordion-toggle"))) {
                probableAccordionToggle.classList.remove("active");
              }
            }
          });
          if (targetElement.parentElement.previousElementSibling.offsetWidth === targetElement.parentElement.offsetWidth) {
            ub_switchFocusToTab(ancestorTabIndexes[i], tabBar);
          }
        }
      });
      var targetAccordion = targetElement.previousElementSibling;
      var scrollBoundingRect = (displayModes[0][displayMode] === "accordion" ? targetAccordion : tabContentRoot).getBoundingClientRect();
      setTimeout(function () {
        window.scrollBy(0, (scrollBoundingRect.y || scrollBoundingRect.top) - Math.max.apply(Math, stickyHeaderHeights));
      }, 50);
    }
  }
}
document.addEventListener("DOMContentLoaded", function () {
  Array.prototype.slice.call(document.getElementsByClassName("wp-block-ub-tabbed-content")).forEach(function (instance) {
    return ub_initializeTabBlock(instance);
  });
  Array.prototype.slice.call(document.getElementsByClassName("wp-block-ub-tabbed-content-underline")).forEach(function (instance) {
    return ub_initializeTabBlock(instance);
  });
  Array.prototype.slice.call(document.getElementsByClassName("wp-block-ub-tabbed-content-pills")).forEach(function (instance) {
    return ub_initializeTabBlock(instance);
  });
  ub_hashTabSwitch();
});
function ub_initializeTabBlock(instance) {
  var tabBar = instance.children[0].children[0];
  if (window.getComputedStyle(tabBar).display !== "none") {
    var scrollWidth = tabBar.scrollWidth,
      clientWidth = tabBar.clientWidth;
    Array.prototype.slice.call(tabBar.children).forEach(function (tab) {
      if (tab.classList.contains("active")) {
        tab.setAttribute("tabindex", 0);
        if (scrollWidth > clientWidth) {
          var tabLocation = (tab.getBoundingClientRect().x || tab.getBoundingClientRect().left) + tab.getBoundingClientRect().width - (instance.getBoundingClientRect().x || instance.getBoundingClientRect().left);
          if (tabLocation > clientWidth) {
            tabBar.scrollLeft = tabLocation - clientWidth;
          }
        }
      }
    });
  }
  var displayModes = ub_getTabbedContentDisplayModes(instance);
  var currentDisplay = -1;
  if (window.innerWidth < 700) {
    currentDisplay = 0;
  } else if (window.innerWidth < 900) {
    currentDisplay = 1;
  } else {
    currentDisplay = 2;
  }
  if (displayModes[currentDisplay] === "accordion") {
    var tabContents = instance.children[1];
    Array.prototype.slice.call(tabContents.children).forEach(function (child, i) {
      if (i % 2 === 1) {
        child.setAttribute("role", "region");
        child.removeAttribute("aria-labelledby");
      } else {
        child.setAttribute("aria-expanded", !child.nextElementSibling.classList.contains("ub-hide"));
        child.setAttribute("aria-controls", child.nextElementSibling.id);
        if (child.nextElementSibling.classList.contains("active")) {
          child.classList.add("active");
        }
      }
    });
  } else {
    var _tabBar = instance.children[0].children[0];
    Array.prototype.slice.call(_tabBar.children).forEach(function (tab) {
      tab.addEventListener("keydown", displayModes[currentDisplay] === "verticaltab" ? ub_upDownPress : ub_leftRightPress);
    });
    _tabBar.setAttribute("aria-orientation", displayModes[currentDisplay].slice(0, -3));
  }
}
window.onhashchange = ub_hashTabSwitch;
Array.prototype.slice.call(document.getElementsByClassName("wp-block-ub-tabbed-content-tabs-content")).forEach(function (container) {
  Array.prototype.slice.call(container.children).filter(function (child) {
    return child.classList.contains("wp-block-ub-tabbed-content-accordion-toggle");
  }).forEach(function (accordionToggle, i) {
    accordionToggle.addEventListener("click", function () {
      var root = container.parentElement;
      if (accordionToggle.classList.contains("active")) {
        var activeTabs = JSON.parse(root.dataset.activeTabs);
        if (activeTabs.length > 1) {
          root.dataset.activeTabs = JSON.stringify(activeTabs.filter(function (c) {
            return c !== i;
          }));
        } else {
          root.dataset.noActiveTabs = true;
        }
      } else {
        Array.prototype.slice.call(document.getElementsByClassName("ub_image_slider")).forEach(function (slider) {
          var swiper = new Swiper("#".concat(slider.id), JSON.parse(slider.dataset.swiperData));
        });
        setTimeout(function () {
          window.dispatchEvent(new Event("resize"));
        }, 100);
        if (root.dataset.noActiveTabs) {
          delete root.dataset.noActiveTabs;
          root.dataset.activeTabs = JSON.stringify([i]);
        } else {
          root.dataset.activeTabs = JSON.stringify(JSON.parse(root.dataset.activeTabs).concat(i));
        }
      }
      accordionToggle.classList.toggle("active");
      accordionToggle.nextElementSibling.classList.toggle("active");
      accordionToggle.nextElementSibling.classList.toggle("ub-hide");
    });
  });
});
Array.prototype.slice.call(document.getElementsByClassName("wp-block-ub-tabbed-content-tab-holder")).forEach(function (tabBar) {
  var tabBarIsBeingDragged = false;
  var oldScrollPosition = -1;
  var oldMousePosition = -1;
  var tabBarLocation = tabBar.getBoundingClientRect().x || tabBar.getBoundingClientRect().left;
  tabBar.addEventListener("mousedown", function (e) {
    tabBarIsBeingDragged = true;
    oldScrollPosition = tabBar.scrollLeft;
    oldMousePosition = e.clientX - tabBarLocation;
  });
  document.addEventListener("mouseup", function () {
    if (tabBarIsBeingDragged) {
      tabBarIsBeingDragged = false;
      oldScrollPosition = -1;
      oldMousePosition = -1;
    }
  });
  document.addEventListener("mousemove", function (e) {
    if (tabBarIsBeingDragged) {
      e.preventDefault();
      var newMousePosition = e.clientX - tabBarLocation;
      tabBar.scrollLeft = oldScrollPosition - newMousePosition + oldMousePosition;
    }
  });
});

/***/ }),

/***/ "./src/blocks/table-of-contents/front.js":
/*!***********************************************!*\
  !*** ./src/blocks/table-of-contents/front.js ***!
  \***********************************************/
/***/ (() => {

if (!Element.prototype.matches) {
  Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}
if (!Element.prototype.closest) {
  Element.prototype.closest = function (s) {
    var el = this;
    do {
      if (el.matches(s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);
    return null;
  };
}
function ub_hashHeaderScroll() {
  var scrollType = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "auto";
  var target = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "";
  var offset = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;
  if (window.location.hash) {
    var targetHeading = document.getElementById(window.location.hash.slice(1));
    var probableHeaders;
    try {
      probableHeaders = document.elementsFromPoint(window.innerWidth / 2, 0);
    } catch (e) {
      probableHeaders = document.msElementsFromPoint(window.innerWidth / 2, 0);
    }
    var stickyHeaders = Array.prototype.slice.call(probableHeaders).filter(function (e) {
      return ["fixed", "sticky"].includes(window.getComputedStyle(e).position);
    });
    var stickyHeaderHeights = stickyHeaders.map(function (h) {
      return h.offsetHeight;
    });
    var deficit = targetHeading.getBoundingClientRect().y || targetHeading.getBoundingClientRect().top;
    switch (scrollType) {
      default:
        window.scrollBy(0, deficit);
        break;
      case "off":
        window.scrollBy(0, deficit);
        break;
      case "auto":
        window.scrollBy(0, deficit - (stickyHeaders.length ? Math.max.apply(Math, stickyHeaderHeights) : 0));
        break;
      case "fixedamount":
        window.scrollBy(0, deficit - offset);
        break;
      case "namedelement":
        window.scrollBy(0, deficit - (document.querySelector(target) ? document.querySelector(target).offsetHeight : 0));
        break;
    }
  }
}
document.addEventListener("DOMContentLoaded", function () {
  var instances = [];
  if (document.getElementById("ub_table-of-contents-toggle-link")) {
    instances.push(document.getElementById("ub_table-of-contents-toggle-link"));
  } else {
    instances = Array.prototype.slice.call(document.getElementsByClassName("ub_table-of-contents-toggle-link"));
  }
  instances.forEach(function (instance) {
    var block = instance.closest(".ub_table-of-contents");
    var tocContainer = block.querySelector(".ub_table-of-contents-container");
    var containerStyle = tocContainer.style;
    var tocExtraContainer = block.querySelector(".ub_table-of-contents-extra-container");
    var extraContainerStyle = tocExtraContainer.style;
    var tocMain = tocExtraContainer.parentNode;
    var mainStyle = block.style;
    var showButton = block.getAttribute("data-showtext") || "show";
    var hideButton = block.getAttribute("data-hidetext") || "hide";
    tocContainer.removeAttribute("style");
    var padding = 60;
    function mobileEvent(mql) {
      if (mql.matches) {
        if (!tocMain.classList.contains("ub_table-of-contents-collapsed")) {
          tocMain.classList.add("ub_table-of-contents-collapsed");
          instance.innerHTML = showButton;
          tocContainer.classList.add("ub-hide");
        }
      } else if (JSON.parse(tocMain.dataset.initiallyshow)) {
        tocMain.classList.remove("ub_table-of-contents-collapsed");
        instance.innerHTML = hideButton;
        tocContainer.classList.remove("ub-hide");
      }
    }
    var mobileQuery = window.matchMedia("(max-width: 800px)");
    if (JSON.parse(block.getAttribute("data-initiallyhideonmobile"))) {
      mobileQuery.addListener(mobileEvent);
    }
    instance.addEventListener("click", function (event) {
      event.preventDefault();
      var curWidth = block.offsetWidth;
      if (block.classList.contains("ub_table-of-contents-collapsed")) {
        //begin showing
        tocExtraContainer.classList.remove("ub-hide");
        tocContainer.classList.remove("ub-hide");
        var targetHeight = tocExtraContainer.offsetHeight + padding / 2; //doesn't include padding
        tocContainer.classList.add("ub-hiding");
        tocExtraContainer.classList.add("ub-hiding");
        mainStyle.width = "".concat(curWidth, "px"); //also take into account number of columns

        setTimeout(function () {
          mainStyle.width = "auto";
          block.classList.remove("ub_table-of-contents-collapsed");
          var fullWidth = getComputedStyle(block).width.slice(0, -2);
          mainStyle.width = "".concat(curWidth, "px");
          setTimeout(function () {
            Object.assign(containerStyle, {
              height: "".concat(targetHeight, "px"),
              width: "100px"
            });
            Object.assign(extraContainerStyle, {
              height: "".concat(targetHeight, "px"),
              width: "100px"
            });
            tocContainer.classList.remove("ub-hiding");
            tocExtraContainer.classList.remove("ub-hiding");
            mainStyle.width = "".concat(fullWidth, "px");
            setTimeout(function () {
              tocContainer.style.width = "".concat(fullWidth - padding, "px");
              tocExtraContainer.style.width = "".concat(fullWidth - padding, "px");
            }, 50);
          }, 50);
        }, 50);
      } else {
        //begin hiding
        mainStyle.width = "".concat(block.offsetWidth, "px");
        Object.assign(containerStyle, {
          height: "".concat(tocContainer.offsetHeight, "px"),
          width: "".concat(tocContainer.offsetWidth, "px")
        });
        Object.assign(extraContainerStyle, {
          height: "".concat(tocExtraContainer.offsetHeight, "px"),
          width: "".concat(tocExtraContainer.offsetWidth, "px")
        });
        setTimeout(function () {
          tocContainer.classList.add("ub-hiding");
          Object.assign(containerStyle, {
            height: "0",
            width: "0"
          });
          Object.assign(extraContainerStyle, {
            height: "0",
            width: "0"
          });
          block.classList.add("ub_table-of-contents-collapsed");
          padding = parseInt(getComputedStyle(tocExtraContainer).paddingLeft.slice(0, -2)) + parseInt(getComputedStyle(tocExtraContainer).paddingRight.slice(0, -2));

          //measure width of toc title + toggle button, then use it as width of block

          mainStyle.width = "".concat(5 + padding + instance.closest(".ub_table-of-contents-header-container").scrollWidth, "px");
        }, 50);
      }
      instance.innerHTML = tocContainer.classList.contains("ub-hiding") ? hideButton : showButton;
      mobileQuery.removeListener(mobileEvent);
    });
    tocContainer.addEventListener("transitionend", function () {
      if (tocContainer.offsetHeight === 0) {
        //hiding is done
        tocContainer.classList.remove("ub-hiding");
        tocContainer.classList.add("ub-hide");
        tocExtraContainer.classList.remove("ub-hiding");
        tocExtraContainer.classList.add("ub-hide");
        if (containerStyle.display === "block") {
          containerStyle.display = "";
        }
        if (extraContainerStyle.display === "block") {
          extraContainerStyle.display = "";
        }
        mainStyle.minWidth = "";
      }
      Object.assign(containerStyle, {
        height: "",
        width: ""
      });
      Object.assign(extraContainerStyle, {
        height: "",
        width: ""
      });
      mainStyle.width = "";
    });
  });
  if (window.location.hash) {
    var sourceToC = document.querySelector(".ub_table-of-contents");
    if (sourceToC) {
      var type = sourceToC.dataset.scrolltype;
      var offset = type === "fixedamount" ? sourceToC.dataset.scrollamount : 0;
      var target = type === "namedelement" ? sourceToC.dataset.scrolltarget : "";
      setTimeout(function () {
        return ub_hashHeaderScroll(type, target, offset);
      }, 50);
    }
  }

  // Link to divider block.
  var tableOfContents = document.querySelectorAll('.ub_table-of-contents[data-linktodivider="true"]');
  var dividers = document.querySelectorAll(".wp-block-ub-divider:not(.ub-divider-orientation-vertical)");
  if (tableOfContents.length > 0 && dividers.length > 0) {
    dividers.forEach(function (divider) {
      var closestTOC = null;
      var minDistance = Infinity;
      tableOfContents.forEach(function (toc) {
        var dividerTop = divider.getBoundingClientRect().top;
        var tocTop = toc.getBoundingClientRect().top;
        var distance = Math.abs(dividerTop - tocTop);
        if (distance < minDistance) {
          minDistance = distance;
          closestTOC = toc;
        }
      });
      if (closestTOC) {
        var linkToTOC = document.createElement("a");
        linkToTOC.href = "#".concat(closestTOC.getAttribute("id"));
        linkToTOC.classList.add("ub-linked-to-toc");
        linkToTOC.innerText = "Table Of Contents";
        divider.appendChild(linkToTOC);
      }
    });
  }
  var smoothScrollElements = document.querySelectorAll('[data-enablesmoothscroll="true"]');
  smoothScrollElements.forEach(function (element) {
    element.style.scrollBehavior = "smooth";
  });
});
window.onhashchange = function () {
  var sourceToC = document.querySelector(".ub_table-of-contents");
  if (sourceToC) {
    var type = sourceToC.dataset.scrolltype;
    var offset = type === "fixedamount" ? sourceToC.dataset.scrollamount : 0;
    var target = type === "namedelement" ? sourceToC.dataset.scrolltarget : "";
    ub_hashHeaderScroll(type, target, offset);
  }
};
Array.prototype.slice.call(document.querySelectorAll(".ub_table-of-contents-container li > a")).forEach(function (link) {
  link.addEventListener("click", function (e) {
    var hashlessLink = link.href.replace(link.hash, "");
    var targetPageNumber = /[?&]page=\d+/g.exec(hashlessLink);
    var currentPageNumber = /[?&]page=\d+/g.exec(window.location.search);
    if (window.location.href.includes(hashlessLink) && (currentPageNumber === null || targetPageNumber && currentPageNumber[0] === targetPageNumber[0])) {
      var tocData = link.closest(".ub_table-of-contents").dataset;
      var type = tocData.scrolltype;
      var offset = type === "fixedamount" ? tocData.scrollamount : 0;
      var target = type === "namedelement" ? tocData.scrolltarget : "";
      e.preventDefault();
      history.pushState(null, "", link.hash);
      ub_hashHeaderScroll(type, target, offset);
    }
  });
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!**********************!*\
  !*** ./src/front.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _blocks_advanced_video_front_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./blocks/advanced-video/front.js */ "./src/blocks/advanced-video/front.js");
/* harmony import */ var _blocks_advanced_video_front_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_blocks_advanced_video_front_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _blocks_button_front_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./blocks/button/front.js */ "./src/blocks/button/front.js");
/* harmony import */ var _blocks_button_front_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_blocks_button_front_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _blocks_content_filter_front_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./blocks/content-filter/front.js */ "./src/blocks/content-filter/front.js");
/* harmony import */ var _blocks_content_filter_front_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_blocks_content_filter_front_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _blocks_content_toggle_front_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./blocks/content-toggle/front.js */ "./src/blocks/content-toggle/front.js");
/* harmony import */ var _blocks_content_toggle_front_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_blocks_content_toggle_front_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _blocks_counter_front_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./blocks/counter/front.js */ "./src/blocks/counter/front.js");
/* harmony import */ var _blocks_counter_front_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_blocks_counter_front_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _blocks_countdown_front_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./blocks/countdown/front.js */ "./src/blocks/countdown/front.js");
/* harmony import */ var _blocks_countdown_front_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_blocks_countdown_front_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _blocks_expand_front_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./blocks/expand/front.js */ "./src/blocks/expand/front.js");
/* harmony import */ var _blocks_expand_front_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_blocks_expand_front_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _blocks_image_slider_front_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./blocks/image-slider/front.js */ "./src/blocks/image-slider/front.js");
/* harmony import */ var _blocks_image_slider_front_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_blocks_image_slider_front_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _blocks_progress_bar_front_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./blocks/progress-bar/front.js */ "./src/blocks/progress-bar/front.js");
/* harmony import */ var _blocks_progress_bar_front_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_blocks_progress_bar_front_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _blocks_tabbed_content_front_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./blocks/tabbed-content/front.js */ "./src/blocks/tabbed-content/front.js");
/* harmony import */ var _blocks_tabbed_content_front_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_blocks_tabbed_content_front_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _blocks_table_of_contents_front_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./blocks/table-of-contents/front.js */ "./src/blocks/table-of-contents/front.js");
/* harmony import */ var _blocks_table_of_contents_front_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_blocks_table_of_contents_front_js__WEBPACK_IMPORTED_MODULE_10__);
// Import all front.js files from blocks











})();

/******/ })()
;
//# sourceMappingURL=ultimate_blocks-frontend-script.build.js.map