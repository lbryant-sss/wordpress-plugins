
    window.wc.priceFormat = (function (e) {
        var r = {};
        function t(n) {
            if (r[n]) return r[n].exports;
            var o = (r[n] = { i: n, l: !1, exports: {} });
            return e[n].call(o.exports, o, o.exports, t), (o.l = !0), o.exports;
        }
        return (
            (t.m = e),
            (t.c = r),
            (t.d = function (e, r, n) {
                t.o(e, r) || Object.defineProperty(e, r, { enumerable: !0, get: n });
            }),
            (t.r = function (e) {
                "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, { value: "Module" }), Object.defineProperty(e, "__esModule", { value: !0 });
            }),
            (t.t = function (e, r) {
                if ((1 & r && (e = t(e)), 8 & r)) return e;
                if (4 & r && "object" == typeof e && e && e.__esModule) return e;
                var n = Object.create(null);
                if ((t.r(n), Object.defineProperty(n, "default", { enumerable: !0, value: e }), 2 & r && "string" != typeof e))
                    for (var o in e)
                        t.d(
                            n,
                            o,
                            function (r) {
                                return e[r];
                            }.bind(null, o)
                        );
                return n;
            }),
            (t.n = function (e) {
                var r =
                    e && e.__esModule
                        ? function () {
                              return e.default;
                          }
                        : function () {
                              return e;
                          };
                return t.d(r, "a", r), r;
            }),
            (t.o = function (e, r) {
                return Object.prototype.hasOwnProperty.call(e, r);
            }),
            (t.p = ""),
            t((t.s = 142))
        );
    })({
        142: function (e, r, t) {
            "use strict";
            t.r(r),
                t.d(r, "getCurrencyFromPriceResponse", function () {
                    return u;
                }),
                t.d(r, "getCurrency", function () {
                    return a;
                }),
                t.d(r, "formatPrice", function () {
                    return f;
                });
            var n = t(5);
            const o = {
                code: n.CURRENCY.code,
                symbol: n.CURRENCY.symbol,
                thousandSeparator: n.CURRENCY.thousandSeparator,
                decimalSeparator: n.CURRENCY.decimalSeparator,
                minorUnit: n.CURRENCY.precision,
                prefix: ((i = n.CURRENCY.symbol), (c = n.CURRENCY.symbolPosition), { left: i, left_space: " " + i, right: "", right_space: "" }[c] || ""),
                suffix: ((e, r) => ({ left: "", left_space: "", right: e, right_space: " " + e }[r] || ""))(n.CURRENCY.symbol, n.CURRENCY.symbolPosition),
            };
            var i, c;
            const u = (e) => {
                    if (null == e || !e.currency_code) return o;
                    const { currency_code: r, currency_symbol: t, currency_thousand_separator: n, currency_decimal_separator: i, currency_minor_unit: c, currency_prefix: u, currency_suffix: a } = e;
                    return {
                        code: r || "USD",
                        symbol: t || "$",
                        thousandSeparator: "string" == typeof n ? n : ",",
                        decimalSeparator: "string" == typeof i ? i : ".",
                        minorUnit: Number.isFinite(c) ? c : 2,
                        prefix: "string" == typeof u ? u : "$",
                        suffix: "string" == typeof a ? a : "",
                    };
                },
                a = function () {
                    let e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {};
                    return { ...o, ...e };
                },
                f = (e, r) => {

                    if ("" === e || void 0 === e) return "";
		    if (typeof woocs_current_currency != 'undefined') {
			//e = e * woocs_current_currency['rate'];
			//e = parseInt(e, 10);
			//e = Math.round(e);
	
		    }
                    const t = "number" == typeof e ? e : parseInt(e, 10);
                    if (!Number.isFinite(t)) return "";
	
                    const n = a(r),
                        { minorUnit: o, prefix: i, suffix: c, decimalSeparator: u, thousandSeparator: f } = n,
                        s = t / 10 ** o,
			
                        { beforeDecimal: l, afterDecimal: p } = ((e) => {
                            const r = e.split(".");
                            return { beforeDecimal: r[0], afterDecimal: r[1] || "" };
                        })(s.toString()),
                        d = `${i}${((e, r) => e.replace(/\B(?=(\d{3})+(?!\d))/g, r))(l, f)}${((e, r, t) => (e ? `${r}${e.padEnd(t, "0")}` : t > 0 ? `${r}${"0".repeat(t)}` : ""))(p, u, o)}${c}`,
                        m = document.createElement("textarea");
			 
                    return (m.innerHTML = d), m.value;
                };
        },
        5: function (e, r) {
            e.exports = window.wc.wcSettings;
        },
    });
