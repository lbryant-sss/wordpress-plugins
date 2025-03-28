var ctf_js_exists = (typeof ctf_js_exists !== 'undefined') ? true : false;
if(!ctf_js_exists){

    (function ($) {

        window.ctf_init = function() {
            window.ctfObject = {};
            if ($('.ctf').length
                && typeof $('.ctf').first().attr('data-ctf-flags') !== 'undefined') {
                var flags = $('.ctf').first().attr('data-ctf-flags').split(',');

                if (flags.indexOf('gdpr') > -1) {
                    window.ctfObject.consentGiven = false;
                    window.ctfObject.gdpr = true;
                } else {
                    window.ctfObject.consentGiven = true;
                    window.ctfObject.gdpr = false;
                }

                if (flags.indexOf('locator') > -1) {
                    var random = Math.floor(Math.random() * $('.ctf').length);
                    window.ctfObject.locator = $('.ctf').length === 1 || (random === 1);
                } else {
                    window.ctfObject.locator = false;
                }

            } else {
                window.ctfObject.consentGiven = true;
                window.ctfObject.gdpr = false;
                window.ctfObject.locator = false;
            }

            if ($('.ctf').length <= $('.ctf_is_initialized').length) {
                return;
            }
            if (window.ctfObject.consentGiven) {
                ctfMaybeAddIntents();
            }
            //Runs every time new tweets are loaded
            function ctfScripts($ctf) {
                $ctf.addClass('ctf_is_initialized');

                if (window.ctfObject.locator) {
                    var feedID = typeof $ctf.attr('data-feed-id') ? $ctf.attr('data-feed-id') : 'ctf-single',
                        postID = typeof $ctf.attr('data-postid') ? $ctf.attr('data-postid') : '';

                    jQuery.ajax({
                        url: ctf.ajax_url,
                        type: 'post',
                        data: {
                            action: 'ctf_do_locator',
                            atts: $ctf.attr('data-ctfshortcode'),
                            feed_id: feedID,
                            location: ctfLocationGuess($ctf),
                            post_id: postID,
                        },
                        success: function (data) {
                        }
                    }); // ajax call
                }

                if (ctfCheckConsent()) {
                    ctfRemovePrivacyFeatures($ctf);
                } else {
                    ctfApplyPrivacyFeatures($ctf);
                }
                //Loop through each newly loaded tweet
                $ctf.find('.ctf-item.ctf-new').each(function () {

                    var $ctfItem = $(this),
                        $ctfTextMedia = $ctfItem.find('.ctf-tweet-text-media-wrap'),
                        $ctfText = $ctfItem.find('.ctf-tweet-text').remove('.ctf-tweet-text-media-wrap'),
                        ctfTextStr = ' ' + $ctfText.html();

                    if ($ctf.attr('data-ctfdisablelinks') != 'true' && typeof ctfTextStr !== 'undefined' && !$ctf.find('.ctf-tweet-text-link').length) {

                        var ctfLinkColor = $ctf.attr('data-ctflinktextcolor'),
                            ctfLinkColorHex = '';
                        if (ctfLinkColor) ctfLinkColorHex = ctfLinkColor.replace(';', '').split("#")[1];

                        //Link URLs
                        window.ctfLinkify = (function () {
                            var k = "[a-z\\d.-]+://",
                                h = "(?:(?:[0-9]|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])\\.){3}(?:[0-9]|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])",
                                c = "(?:(?:[^\\s!@#$%^&*()_=+[\\]{}\\\\|;:'\",.<>/?]+)\\.)+",
                                n = "(?:ac|ad|aero|ae|af|ag|ai|al|am|an|ao|aq|arpa|ar|asia|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|biz|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|cat|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|coop|com|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|info|int|in|io|iq|ir|is|it|je|jm|jobs|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mobi|mo|mp|mq|mr|ms|mt|museum|mu|mv|mw|mx|my|mz|name|na|nc|net|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pro|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tel|tf|tg|th|tj|tk|tl|tm|tn|to|tp|travel|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|xn--0zwm56d|xn--11b5bs3a9aj6g|xn--80akhbyknj4f|xn--9t4b11yi5a|xn--deba0ad|xn--g6w251d|xn--hgbk6aj7f53bba|xn--hlcj6aya9esc7a|xn--jxalpdlp|xn--kgbechtv|xn--zckzah|ye|yt|yu|za|zm|zw)",
                                f = "(?:" + c + n + "|" + h + ")", o = "(?:[;/][^#?<>\\s]*)?",
                                e = "(?:\\?[^#<>\\s]*)?(?:#[^<>\\s]*)?", d = "\\b" + k + "[^<>\\s]+",
                                a = "\\b" + f + o + e + "(?!\\w)", m = "mailto:",
                                j = "(?:" + m + ")?[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@" + f + e + "(?!\\w)",
                                l = new RegExp("(?:" + d + "|" + a + "|" + j + ")", "ig"), g = new RegExp("^" + k, "i"),
                                b = {"'": "`", ">": "<", ")": "(", "]": "[", "}": "{", "B;": "B+", "b:": "b9"}, i = {
                                    callback: function (q, p) {
                                        return p ? '<a href="' + p + '" title="' + p + '" target="_blank">' + q + "</a>" : q
                                    },
                                    punct_regexp: /(?:[!?.,:;'"]|(?:&|&amp;)(?:lt|gt|quot|apos|raquo|laquo|rsaquo|lsaquo);)$/
                                };
                            return function (u, z) {
                                z = z || {};
                                var w, v, A, p, x = "", t = [], s, E, C, y, q, D, B, r;
                                for (v in i) {
                                    if (z[v] === undefined) {
                                        z[v] = i[v]
                                    }
                                }
                                while (w = l.exec(u)) {
                                    A = w[0];
                                    E = l.lastIndex;
                                    C = E - A.length;
                                    if (/[\/:]/.test(u.charAt(C - 1))) {
                                        continue
                                    }
                                    do {
                                        y = A;
                                        r = A.substr(-1);
                                        B = b[r];
                                        if (B) {
                                            q = A.match(new RegExp("\\" + B + "(?!$)", "g"));
                                            D = A.match(new RegExp("\\" + r, "g"));
                                            if ((q ? q.length : 0) < (D ? D.length : 0)) {
                                                A = A.substr(0, A.length - 1);
                                                E--
                                            }
                                        }
                                        if (z.punct_regexp) {
                                            A = A.replace(z.punct_regexp, function (F) {
                                                E -= F.length;
                                                return ""
                                            })
                                        }
                                    } while (A.length && A !== y);
                                    p = A;
                                    if (!g.test(p)) {
                                        p = (p.indexOf("@") !== -1 ? (!p.indexOf(m) ? "" : m) : !p.indexOf("irc.") ? "irc://" : !p.indexOf("ftp.") ? "ftp://" : "http://") + p
                                    }
                                    if (s != C) {
                                        t.push([u.slice(s, C)]);
                                        s = E
                                    }
                                    t.push([A, p])
                                }
                                t.push([u.substr(s)]);
                                for (v = 0; v < t.length; v++) {
                                    x += z.callback.apply(window, t[v])
                                }
                                return x || u
                            }
                        })();
                        if (!$ctfText.find('a').length) {
                            $ctfText.find('.emoji').each(function() {
                                $(this).replaceWith($(this).attr('alt'));
                            });
                            ctfTextStr = ' ' +$ctfText.html();
                            ctfTextStr = ctfLinkify(ctfTextStr);
                        }
                        //Link hashtags
                        var ctfHashRegex = /(^|\s)#(\w*[\u0041-\u005A\u0061-\u007A\u00AA\u00B5\u00BA\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0370-\u0374\u0376\u0377\u037A-\u037D\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03F5\u03F7-\u0481\u048A-\u0527\u0531-\u0556\u0559\u0561-\u0587\u05D0-\u05EA\u05F0-\u05F2\u0620-\u064A\u066E\u066F\u0671-\u06D3\u06D5\u06E5\u06E6\u06EE\u06EF\u06FA-\u06FC\u06FF\u0710\u0712-\u072F\u074D-\u07A5\u07B1\u07CA-\u07EA\u07F4\u07F5\u07FA\u0800-\u0815\u081A\u0824\u0828\u0840-\u0858\u08A0\u08A2-\u08AC\u0904-\u0939\u093D\u0950\u0958-\u0961\u0971-\u0977\u0979-\u097F\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD\u09CE\u09DC\u09DD\u09DF-\u09E1\u09F0\u09F1\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A59-\u0A5C\u0A5E\u0A72-\u0A74\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD\u0AD0\u0AE0\u0AE1\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D\u0B5C\u0B5D\u0B5F-\u0B61\u0B71\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BD0\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C33\u0C35-\u0C39\u0C3D\u0C58\u0C59\u0C60\u0C61\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD\u0CDE\u0CE0\u0CE1\u0CF1\u0CF2\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D\u0D4E\u0D60\u0D61\u0D7A-\u0D7F\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0E01-\u0E30\u0E32\u0E33\u0E40-\u0E46\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB0\u0EB2\u0EB3\u0EBD\u0EC0-\u0EC4\u0EC6\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F88-\u0F8C\u1000-\u102A\u103F\u1050-\u1055\u105A-\u105D\u1061\u1065\u1066\u106E-\u1070\u1075-\u1081\u108E\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u1380-\u138F\u13A0-\u13F4\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u1700-\u170C\u170E-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176C\u176E-\u1770\u1780-\u17B3\u17D7\u17DC\u1820-\u1877\u1880-\u18A8\u18AA\u18B0-\u18F5\u1900-\u191C\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19C1-\u19C7\u1A00-\u1A16\u1A20-\u1A54\u1AA7\u1B05-\u1B33\u1B45-\u1B4B\u1B83-\u1BA0\u1BAE\u1BAF\u1BBA-\u1BE5\u1C00-\u1C23\u1C4D-\u1C4F\u1C5A-\u1C7D\u1CE9-\u1CEC\u1CEE-\u1CF1\u1CF5\u1CF6\u1D00-\u1DBF\u1E00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FBC\u1FBE\u1FC2-\u1FC4\u1FC6-\u1FCC\u1FD0-\u1FD3\u1FD6-\u1FDB\u1FE0-\u1FEC\u1FF2-\u1FF4\u1FF6-\u1FFC\u2071\u207F\u2090-\u209C\u2102\u2107\u210A-\u2113\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u212F-\u2139\u213C-\u213F\u2145-\u2149\u214E\u2183\u2184\u2C00-\u2C2E\u2C30-\u2C5E\u2C60-\u2CE4\u2CEB-\u2CEE\u2CF2\u2CF3\u2D00-\u2D25\u2D27\u2D2D\u2D30-\u2D67\u2D6F\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u2E2F\u3005\u3006\u3031-\u3035\u303B\u303C\u3041-\u3096\u309D-\u309F\u30A1-\u30FA\u30FC-\u30FF\u3105-\u312D\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FCC\uA000-\uA48C\uA4D0-\uA4FD\uA500-\uA60C\uA610-\uA61F\uA62A\uA62B\uA640-\uA66E\uA67F-\uA697\uA6A0-\uA6E5\uA717-\uA71F\uA722-\uA788\uA78B-\uA78E\uA790-\uA793\uA7A0-\uA7AA\uA7F8-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA822\uA840-\uA873\uA882-\uA8B3\uA8F2-\uA8F7\uA8FB\uA90A-\uA925\uA930-\uA946\uA960-\uA97C\uA984-\uA9B2\uA9CF\uAA00-\uAA28\uAA40-\uAA42\uAA44-\uAA4B\uAA60-\uAA76\uAA7A\uAA80-\uAAAF\uAAB1\uAAB5\uAAB6\uAAB9-\uAABD\uAAC0\uAAC2\uAADB-\uAADD\uAAE0-\uAAEA\uAAF2-\uAAF4\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uABC0-\uABE2\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB00-\uFB06\uFB13-\uFB17\uFB1D\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF21-\uFF3A\uFF41-\uFF5A\uFF66-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC]+\w*)/gi;

                        function ctfHashReplacer(hash) {
                            //Remove white space at beginning of hash
                            var replacementString = hash.trim();
                            //If the hash is a hex code then don't replace it with a link as it's likely in the style attr, eg: "color: #ff0000"
                            if (/^#[0-9A-F]{6}$/i.test(replacementString)) {
                                return replacementString;
                            } else {
                                return ' <a href="https://twitter.com/hashtag/' + replacementString.substring(1) + '" target="_blank" rel="nofollow noopener">' + replacementString + '</a>';
                            }
                        }

                        //Replace hashtags in text
                        if (ctfTextStr.length > 0) {
                            //Add a space after all <br> tags so that #hashtags immediately after them are also converted to hashtag links. Without the space they aren't captured by the regex.
                            ctfTextStr = ctfTextStr.replace(/<br>/g, "<br> ");
                            ctfTextStr = ctfTextStr.replace(ctfHashRegex, ctfHashReplacer);
                        }

                        //Link @tags
                        function ctfReplaceTags(tag) {
                            var replacementString = tag.trim();
                            return ' <a href="https://twitter.com/' + replacementString.substring(1) + '" target="_blank" rel="nofollow noopener">' + replacementString + '</a>';
                        }

                        var tagRegex = /[\s][@]+[A-Za-z0-9-_]+/g;
                        ctfTextStr = ctfTextStr.replace(tagRegex, ctfReplaceTags);


                        //Replace text with linked version
                        $ctfText.html(ctfTextStr.trim());
                        $ctfText.append($ctfTextMedia);

                        //Add link color
                        $ctfText.find('a').css('color', '#' + ctfLinkColorHex);
                        $ctfTextMedia.css('color', '#' + ctfLinkColorHex);

                    } // End "ctfdata-disablelinks" check

                    // shorten long urls in tweets
                    $ctfItem.find('.ctf-tweet-text a').each(function () {
                        if (jQuery(this).text().indexOf('http') > -1 && jQuery(this).text().length > 63) {
                            jQuery(this).text(jQuery(this).text().substring(0, 60) + '...');
                        }
                    });

                }); // End .ctfItem loop

                //Change color of retweet icon to match text
                // $ctf.find('.ctf-retweet-icon').css({'background' : $ctf.find('.ctf-tweet-text a').css('color')}); //This doesn't work well if the link color is set to white as the default color of the icon text is also white

                //Change colors of some items to match tweet text
                $ctf.find('.ctf-author-name, .ctf-tweet-date, .ctf-author-screenname, .ctf-twitterlink, .ctf-author-box-link, .ctf-retweet-text, .ctf-quoted-tweet').css('color', $ctf.find('.ctf-tweet-text').css('color'));

                $ctf.find('.ctf_more').off('click').on('click', function (e) {
                    e.preventDefault();
                    $(this).hide().next('.ctf_remaining').show();
                });

                // Call Custom JS if it exists
                if (typeof ctf_custom_js == 'function') ctf_custom_js($);

                $ctf.find('.ctf-author-box-link p:empty').remove();
            } // end ctfScripts()

            function ctfLoadTweets(lastIDData, shortcodeData, $ctf, $ctfMore, numNeeded, persistentIndex) {

                //Display loader
                $ctfMore.addClass('ctf-loading').append('<div class="ctf-loader"></div>');
                $ctfMore.find('.ctf-loader').css('background-color', $ctfMore.css('color'));

                var feedID = typeof $ctf.attr('data-feedid') ? $ctf.attr('data-feedid') : 'ctf-single',
                    postID = typeof $ctf.attr('data-postid') ? $ctf.attr('data-postid') : '',
                    v2feed = typeof $ctf.attr('data-feed') ? $ctf.attr('data-feed') : '';


                jQuery.ajax({
                    url: ctf.ajax_url,
                    type: 'post',
                    data: {
                        action: 'ctf_get_more_posts',
                        last_id_data: lastIDData,
                        shortcode_data: shortcodeData,
                        num_needed: numNeeded,
                        persistent_index: persistentIndex,
                        feed_id: feedID,
                        location: ctfLocationGuess($ctf),
                        post_id: postID,
                        v2feed: v2feed,
                    },
                    success: function (data) {
                        if (lastIDData !== '') {
                            // appends the html echoed out in ctf_get_new_posts() to the last post element
                            if (data.indexOf('<meta charset') == -1) {
                                $ctf.find('.ctf-item').removeClass('ctf-new').last().after(data);
                            }

                            if ($ctf.find('.ctf-out-of-tweets').length) {
                                $ctfMore.hide();
                                //Fade in the no more tweets message
                                $ctf.find('.ctf-out-of-tweets p').eq(0).fadeIn().end().eq(1).delay(500).fadeIn();
                            }
                        } else {
                            $ctf.find('.ctf-tweets').append(data);
                        }


                        //Remove loader
                        $ctfMore.removeClass('ctf-loading').find('.ctf-loader').remove();

                        //Re-run JS code
                        ctfScripts($ctf);

                    }
                }); // ajax call
            }

            $('.ctf').each(function () {

                var $ctf = $(this),
                    numNeeded = parseInt($ctf.attr('data-ctfneeded'));

                //Adds a class if the feed is in a narrow column or on mobile so we can make styling adjustments
                if ($ctf.width() <= 480) $ctf.addClass('ctf-narrow');
                if ($ctf.width() <= 320) $ctf.addClass('ctf-super-narrow');

                if (!$(this).hasClass('ctf_is_initialized')) {
                    ctfScripts($ctf);
                }

                // delay added to prevent strange issue with ajax themes returning the entire page
                setTimeout(function () {
                    if (numNeeded > 0) {
                        var $ctfMore = $ctf.find('.ctf-more'),
                            lastIDData = $ctf.find('.ctf-item').last().attr('id'),
                            persistentIndex = $ctf.find('.ctf-item').length,
                            shortcodeData = $ctf.attr('data-ctfshortcode');

                        ctfLoadTweets(lastIDData, shortcodeData, $ctf, $ctfMore, numNeeded, persistentIndex);
                    }
                }, 500);

                // add the load more button and input to simulate a dynamic json file call
                $ctf.find('.ctf-more').on('click', function () {
                    // read the json that is in the ctf-shortcode-data that contains all of the shortcode arguments
                    var $ctfMore = $(this),
                        lastIDData = $ctf.find('.ctf-item').last().attr('id'),
                        persistentIndex = $ctf.find('.ctf-item').length,
                        shortcodeData = $ctf.attr('data-ctfshortcode');

                    ctfLoadTweets(lastIDData, shortcodeData, $ctf, $ctfMore, 0, persistentIndex);

                });

                $ctf.find('.ctf-author-box-link p:empty').remove();
                setTimeout(function () {
                    if (ctfCheckConsent()) {
                        ctfRemovePrivacyFeatures($ctf);
                    } else {
                        ctfApplyPrivacyFeatures($ctf);
                    }
                }, 500);
            }); // end .cff each loop


        }

        jQuery(document).ready(function($) {
            ctf_init();

            // Cookie Notice by dFactory
            $('#cookie-notice a').on('click',function() {
                setTimeout(function() {
                    ctfAterConsentToggled();
                },1000);
            });

            // Cookie Notice by dFactory
            $('#cookie-law-info-bar a').on('click',function() {
                setTimeout(function() {
                    ctfAterConsentToggled();
                },1000);
            });

            // GDPR Cookie Consent by WebToffee
            $('.cli-user-preference-checkbox').on('click',function(){
                ctfAterConsentToggled();
            });

            // Cookiebot
            $(window).on('CookiebotOnAccept', function (event) {
                ctfAterConsentToggled();
            });

            // Complianz by Really Simple Plugins
            $(document).on('cmplzEnableScripts', function (event) {
                if ( event.detail === 'marketing' ) {
                    $('.ctf').each(function () {
                        window.ctfObject.consentGiven = true;
                        ctfAterConsentToggled();
                    });
                }
            });

            // Complianz by Really Simple Plugins
            $(document).on('cmplzFireCategories', function (event) {
                if ( event.detail.category === 'marketing' ) {
                    $('.ctf').each(function () {
                        window.ctfObject.consentGiven = true;
                        ctfAterConsentToggled();
                    });
                }
            });

            // Borlabs Cookie by Borlabs
            $(document).on('borlabs-cookie-consent-saved', function (event) {
                ctfAterConsentToggled();
            });

            // devowl.io
            if (typeof window.consentApi !== 'undefined') {
                window.consentApi.consent("custom-twitter-feed").then(() => {
                    try {
                        // applies full features to feed
                        setTimeout(function() {
                            $('.ctf').each(function () {
                                window.ctfObject.consentGiven = true;
                                ctfAterConsentToggled();
                            });
                        },1000);
                    }
                    catch (error) {
                        // do nothing
                    }
                });
            }

            // Moove Agency
            $('.moove-gdpr-infobar-allow-all').on('click',function() {
                setTimeout(function() {
                    $('.ctf').each(function () {
                        window.ctfObject.consentGiven = true;
                        ctfAterConsentToggled();
                    });
                },1000);
            });

            // WPConsent
            window.addEventListener('wpconsent_consent_saved', function(event) {
                setTimeout(function() {
                    $('.ctf').each(function () {
                        ctfAterConsentToggled();
                    });
                },1000);
            });

            window.addEventListener('wpconsent_consent_updated', function(event) {
                setTimeout(function() {
                    $('.ctf').each(function () {
                        ctfAterConsentToggled();
                    });
                },1000);
            });
        });

        function ctfCheckConsent() {
            if (window.ctfObject.consentGiven || !window.ctfObject.gdpr) {
                return true;
            }
            if (typeof window.WPConsent !== 'undefined') {
                window.ctfObject.consentGiven = window.WPConsent.hasConsent('marketing');
            } else if (typeof CLI_Cookie !== "undefined") { // GDPR Cookie Consent by WebToffee
                if (CLI_Cookie.read(CLI_ACCEPT_COOKIE_NAME) !== null)  {

                    // WebToffee no longer uses this cookie but being left here to maintain backwards compatibility
                    if (CLI_Cookie.read('cookielawinfo-checkbox-non-necessary') !== null) {
                        window.ctfObject.consentGiven = CLI_Cookie.read('cookielawinfo-checkbox-non-necessary') === 'yes';
                    }

                    if (CLI_Cookie.read('cookielawinfo-checkbox-necessary') !== null) {
                        window.ctfObject.consentGiven = CLI_Cookie.read('cookielawinfo-checkbox-necessary') === 'yes';
                    }
                }

            } else if (typeof window.cnArgs !== "undefined") { // Cookie Notice by dFactory
                var value = "; " + document.cookie,
                    parts = value.split( '; cookie_notice_accepted=' );

                if ( parts.length === 2 ) {
                    var val = parts.pop().split( ';' ).shift();

                    window.ctfObject.consentGiven = (val === 'true');
                }
            } else if (typeof window.cookieconsent !== 'undefined') { // Complianz by Really Simple Plugins
                window.ctfObject.consentGiven = ( ctfCmplzGetCookie('cmplz_consent_status') === 'allow' || jQuery('body').hasClass('cmplz-status-marketing') );
            } else if (typeof window.Cookiebot !== "undefined") { // Cookiebot by Cybot A/S
                window.ctfObject.consentGiven = Cookiebot.consented;
            } else if (typeof window.BorlabsCookie !== 'undefined') { // Borlabs Cookie by Borlabs
                window.ctfObject.consentGiven = typeof window.BorlabsCookie.Consents !== 'undefined' ? window.BorlabsCookie.Consents.hasConsent('twitter') : window.BorlabsCookie.checkCookieConsent('twitter');
            } else if (cffCmplzGetCookie('moove_gdpr_popup')) { // Moove GDPR Popup
                var moove_gdpr_popup = JSON.parse(decodeURIComponent(ctfCmplzGetCookie('moove_gdpr_popup')));
                window.ctfObject.consentGiven = typeof moove_gdpr_popup.thirdparty !== "undefined" && moove_gdpr_popup.thirdparty === "1";
            }

            var evt = jQuery.Event('ctfcheckconsent');
            evt.feed = this;
            jQuery(window).trigger(evt);

            return window.ctfObject.consentGiven; // GDPR not enabled
        }

        function ctfApplyPrivacyFeatures($ctf) {
            if (!$ctf.find('.ctf-hide-avatar').length || $ctf.find('.ctf-hide-avatar.ctf-no-consent').length) {
                $ctf.find('.ctf-item').addClass('ctf-hide-avatar ctf-no-consent');
            }
            if ($('.ctf-header-img span').length) {
                $('.ctf-header-img').addClass('ctf-no-consent');
            }
        }

        function ctfRemovePrivacyFeatures($ctf) {
            ctfMaybeAddIntents();
            $ctf.find('.ctf-item.ctf-no-consent').removeClass('ctf-hide-avatar');
            $ctf.find('.ctf-author-avatar').each(function() {
                $(this).find('span').replaceWith('<img src="'+$(this).find('span').attr('data-avatar')+'" alt="'+$(this).find('span').attr('data-alt')+'" width="48" height="48">');
            });
            $ctf.find('.ctf-header-img').each(function() {
                $(this).find('span').replaceWith('<img src="'+$(this).find('span').attr('data-avatar')+'" alt="'+$(this).find('span').attr('data-alt')+'" width="48" height="48">');
            });
            $ctf.find('.ctf-no-consent').removeClass('ctf-no-consent');
            //Header profile pic hover
            $ctf.find('.ctf-header .ctf-header-link').on('mouseenter mouseleave', function(e) {
                switch(e.type) {
                    case 'mouseenter':
                        $ctf.find('.ctf-header .ctf-header-img-hover').fadeIn(200);
                        break;
                    case 'mouseleave':
                        $ctf.find('.ctf-header .ctf-header-img-hover').stop().fadeOut(600);
                        break;
                }
            });
        }

        function ctfAterConsentToggled() {
            if (ctfCheckConsent()) {
                $('.ctf').each(function () {
                    ctfRemovePrivacyFeatures($(this));
                });
            }
        }

        function ctfMaybeAddIntents() {
            if (typeof window.ctfObject.intentsIncluded === undefined) {
                window.ctfObject.intentsIncluded = false;
            }

            $('.ctf').each(function () {
                if (!window.ctfObject.intentsIncluded && typeof $(this).attr('data-ctfintents') !== undefined) {
                    window.ctfObject.intentsIncluded = true;
                    (function() {
                        if (window.__twitterIntentHandler) return;
                        var intentRegex = /twitter\.com\/intent\/(\w+)/,
                            windowOptions = 'scrollbars=yes,resizable=yes,toolbar=no,location=yes',
                            width = 550,
                            height = 420,
                            winHeight = screen.height,
                            winWidth = screen.width;

                        function handleIntent(e) {
                            e = e || window.event;
                            var target = e.target || e.srcElement,
                                m, left, top;

                            while (target && target.nodeName.toLowerCase() !== 'a') {
                                target = target.parentNode;
                            }

                            if (target && target.nodeName.toLowerCase() === 'a' && target.href) {
                                m = target.href.match(intentRegex);
                                if (m) {
                                    left = Math.round((winWidth / 2) - (width / 2));
                                    top = 0;

                                    if (winHeight > height) {
                                        top = Math.round((winHeight / 2) - (height / 2));
                                    }

                                    window.open(target.href, 'intent', windowOptions + ',width=' + width +
                                        ',height=' + height + ',left=' + left + ',top=' + top);
                                    e.returnValue = false;
                                    e.preventDefault && e.preventDefault();
                                }
                            }
                        }

                        if (document.addEventListener) {
                            document.addEventListener('click', handleIntent, false);
                        } else if (document.attachEvent) {
                            document.attachEvent('onclick', handleIntent);
                        }
                        window.__twitterIntentHandler = true;
                    }());
                }
            });
        }

        function ctfCmplzGetCookie(cname) {
            var name = cname + "="; //Create the cookie name variable with cookie name concatenate with = sign
            var cArr = window.document.cookie.split(';'); //Create cookie array by split the cookie by ';'

            //Loop through the cookies and return the cookie value if it find the cookie name
            for (var i = 0; i < cArr.length; i++) {
                var c = cArr[i].trim();
                //If the name is the cookie string at position 0, we found the cookie and return the cookie value
                if (c.indexOf(name) == 0)
                    return c.substring(name.length, c.length);
            }

            return "";
        }
        function ctfLocationGuess($feed) {
            var location = 'content';

            if ($feed.closest('footer').length) {
                location = 'footer';
            } else if ($feed.closest('.header').length
                || $feed.closest('header').length) {
                location = 'header';
            } else if ($feed.closest('.sidebar').length
                || $feed.closest('aside').length) {
                location = 'sidebar';
            }

            return location;
        }
    })(jQuery);


} //End ctf_js_exists check