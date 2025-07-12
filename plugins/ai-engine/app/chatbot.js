/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./app/js/chatbot/AudioVisualizer.js":
/*!*******************************************!*\
  !*** ./app/js/chatbot/AudioVisualizer.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ AudioVisualizerTwoStreams)
/* harmony export */ });
/**
 * AudioVisualizerTwoStreams.jsx
 *
 * Two circles for user + assistant with volume-based pulsing.
 * Each circle is inside a container of fixed size (circleSize + pulseMaxSize),
 * so we don't affect overall layout when the ring expands.
 *
 * Outer ring has class "mwai-animation" to be styled or animated by your CSS.
 * If userColor / assistantColor is null, we do NOT inline a color.
 */

// React & Vendor Libs
const {
  useState,
  useRef,
  useEffect
} = wp.element;

/**
 * measureVolume()
 * Reads time-domain data and calculates an RMS volume (~0..~20 typical).
 */
function measureVolume(analyser, dataArray) {
  analyser.getByteTimeDomainData(dataArray);
  let sum = 0;
  for (let i = 0; i < dataArray.length; i++) {
    const val = dataArray[i] - 128;
    sum += val * val;
  }
  return Math.sqrt(sum / dataArray.length); // RMS
}
function AudioVisualizerTwoStreams({
  // Streams
  assistantStream = null,
  userStream = null,
  // Colors for the circles
  // If null, we don't apply inline color (you can do it via CSS)
  assistantColor = null,
  userColor = null,
  // UI representations
  // e.g. { emoji: "😊", text: "User", image: "url", use: "image" }
  userUI = {
    emoji: null,
    text: null,
    image: null,
    use: "text"
  },
  assistantUI = {
    emoji: null,
    text: null,
    image: null,
    use: "text"
  },
  // Attack/Release speeds
  attackSpeed = 0.3,
  releaseSpeed = 0.05,
  // Circle sizing
  circleSize = 50,
  // base diameter (inner circle)
  pulseMaxSize = 30 // how many extra px to grow at max volume
}) {
  // Smoothed volumes (state) for rendering
  const [assistantVolume, setAssistantVolume] = useState(0);
  const [userVolume, setUserVolume] = useState(0);

  // We keep references to the raw volumes for attack/release
  const assistantSmoothedRef = useRef(0);
  const userSmoothedRef = useRef(0);

  // AudioContext + Analyser
  const audioContextRef = useRef(null);
  const assistantAnalyserRef = useRef(null);
  const assistantDataArrayRef = useRef(null);
  const userAnalyserRef = useRef(null);
  const userDataArrayRef = useRef(null);
  useEffect(() => {
    // If no streams, do nothing
    if (!assistantStream && !userStream) {
      return;
    }

    // Create or reuse AudioContext
    if (!audioContextRef.current) {
      audioContextRef.current = new AudioContext();
    }
    const audioContext = audioContextRef.current;
    let assistantSource;
    let userSource;
    let animationFrameId;

    // Setup assistant audio
    if (assistantStream) {
      assistantSource = audioContext.createMediaStreamSource(assistantStream);
      assistantAnalyserRef.current = audioContext.createAnalyser();
      assistantAnalyserRef.current.fftSize = 1024;
      assistantDataArrayRef.current = new Uint8Array(assistantAnalyserRef.current.frequencyBinCount);
      assistantSource.connect(assistantAnalyserRef.current);
    }

    // Setup user audio
    if (userStream) {
      userSource = audioContext.createMediaStreamSource(userStream);
      userAnalyserRef.current = audioContext.createAnalyser();
      userAnalyserRef.current.fftSize = 1024;
      userDataArrayRef.current = new Uint8Array(userAnalyserRef.current.frequencyBinCount);
      userSource.connect(userAnalyserRef.current);
    }

    // Animation loop
    const tick = () => {
      let newAssistantVolume = 0;
      if (assistantAnalyserRef.current && assistantDataArrayRef.current) {
        newAssistantVolume = measureVolume(assistantAnalyserRef.current, assistantDataArrayRef.current);
      }
      let newUserVolume = 0;
      if (userAnalyserRef.current && userDataArrayRef.current) {
        newUserVolume = measureVolume(userAnalyserRef.current, userDataArrayRef.current);
      }

      // Attack/release for assistant
      if (newAssistantVolume > assistantSmoothedRef.current) {
        // Attack
        assistantSmoothedRef.current = assistantSmoothedRef.current * (1 - attackSpeed) + newAssistantVolume * attackSpeed;
      } else {
        // Release
        assistantSmoothedRef.current = assistantSmoothedRef.current * (1 - releaseSpeed) + newAssistantVolume * releaseSpeed;
      }

      // Attack/release for user
      if (newUserVolume > userSmoothedRef.current) {
        // Attack
        userSmoothedRef.current = userSmoothedRef.current * (1 - attackSpeed) + newUserVolume * attackSpeed;
      } else {
        // Release
        userSmoothedRef.current = userSmoothedRef.current * (1 - releaseSpeed) + newUserVolume * releaseSpeed;
      }

      // Update state
      setAssistantVolume(assistantSmoothedRef.current);
      setUserVolume(userSmoothedRef.current);

      // Next frame
      animationFrameId = requestAnimationFrame(tick);
    };
    tick();

    // Cleanup
    return () => {
      if (assistantSource) assistantSource.disconnect();
      if (assistantAnalyserRef.current) assistantAnalyserRef.current.disconnect();
      if (userSource) userSource.disconnect();
      if (userAnalyserRef.current) userAnalyserRef.current.disconnect();
      if (animationFrameId) cancelAnimationFrame(animationFrameId);
    };
  }, [assistantStream, userStream, attackSpeed, releaseSpeed]);

  // Convert volumes ~0..20 to [0..1]
  const normAssistant = Math.min(assistantVolume / 20, 1);
  const normUser = Math.min(userVolume / 20, 1);

  // Outer ring diameter = base circleSize + extra pulse
  const assistantPulseDiameter = circleSize + normAssistant * pulseMaxSize;
  const userPulseDiameter = circleSize + normUser * pulseMaxSize;

  // The container is always big enough for the max ring
  const containerSize = circleSize + pulseMaxSize; // e.g. 50 + 30 = 80

  // If userColor/assistantColor is null, skip inline styling for backgroundColor
  const userPulseStyle = {
    width: userPulseDiameter,
    height: userPulseDiameter,
    borderRadius: "50%",
    position: "absolute",
    top: "50%",
    left: "50%",
    transform: "translate(-50%, -50%)",
    opacity: 0.5
  };
  if (userColor) {
    userPulseStyle.backgroundColor = userColor;
  }
  const assistantPulseStyle = {
    width: assistantPulseDiameter,
    height: assistantPulseDiameter,
    borderRadius: "50%",
    position: "absolute",
    top: "50%",
    left: "50%",
    transform: "translate(-50%, -50%)",
    opacity: 0.5
  };
  if (assistantColor) {
    assistantPulseStyle.backgroundColor = assistantColor;
  }
  const userCircleStyle = {
    width: circleSize,
    height: circleSize,
    borderRadius: "50%",
    position: "absolute",
    top: "50%",
    left: "50%",
    transform: "translate(-50%, -50%)",
    display: "flex",
    justifyContent: "center",
    alignItems: "center",
    overflow: "hidden",
    color: "#fff"
  };
  if (userColor) {
    userCircleStyle.backgroundColor = userColor;
  }
  const assistantCircleStyle = {
    width: circleSize,
    height: circleSize,
    borderRadius: "50%",
    position: "absolute",
    top: "50%",
    left: "50%",
    transform: "translate(-50%, -50%)",
    display: "flex",
    justifyContent: "center",
    alignItems: "center",
    overflow: "hidden",
    color: "#fff"
  };
  if (assistantColor) {
    assistantCircleStyle.backgroundColor = assistantColor;
  }

  // Determine who is "talking"
  let containerClass = "mwai-visualizer";
  if (userVolume > assistantVolume) {
    containerClass += " mwai-user-talking";
  } else if (assistantVolume > userVolume) {
    containerClass += " mwai-assistant-talking";
  }

  // Renders the UI inside the fixed circle
  const renderUI = uiObj => {
    if (!uiObj) return null;
    const {
      emoji,
      text,
      image,
      use
    } = uiObj;
    switch (use) {
      case "emoji":
        if (emoji) return /*#__PURE__*/React.createElement("span", null, emoji);
        if (text) return /*#__PURE__*/React.createElement("span", null, text.slice(0, 1));
        return null;
      case "image":
        if (image) {
          return /*#__PURE__*/React.createElement("img", {
            src: image,
            alt: "",
            style: {
              width: "100%",
              height: "100%",
              borderRadius: "50%"
            }
          });
        }
        // fallback to emoji or first-letter
        if (emoji) return /*#__PURE__*/React.createElement("span", null, emoji);
        if (text) return /*#__PURE__*/React.createElement("span", null, text.slice(0, 1));
        return null;
      case "text":
      default:
        if (text) return /*#__PURE__*/React.createElement("span", null, text.slice(0, 1));
        if (emoji) return /*#__PURE__*/React.createElement("span", null, emoji);
        return null;
    }
  };
  return /*#__PURE__*/React.createElement("div", {
    className: containerClass
  }, /*#__PURE__*/React.createElement("div", {
    className: "mwai-visualizer-user",
    style: {
      position: "relative",
      width: containerSize,
      height: containerSize,
      overflow: "visible"
    }
  }, /*#__PURE__*/React.createElement("div", {
    className: "mwai-animation",
    style: userPulseStyle
  }), /*#__PURE__*/React.createElement("div", {
    style: userCircleStyle
  }, renderUI(userUI))), /*#__PURE__*/React.createElement("hr", {
    className: "mwai-visualizer-line"
  }), /*#__PURE__*/React.createElement("div", {
    className: "mwai-visualizer-assistant",
    style: {
      position: "relative",
      width: containerSize,
      height: containerSize,
      overflow: "visible"
    }
  }, /*#__PURE__*/React.createElement("div", {
    className: "mwai-animation",
    style: assistantPulseStyle
  }), /*#__PURE__*/React.createElement("div", {
    style: assistantCircleStyle
  }, renderUI(assistantUI))));
}

/***/ }),

/***/ "./app/js/chatbot/ChatUploadIcon.js":
/*!******************************************!*\
  !*** ./app/js/chatbot/ChatUploadIcon.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _ChatbotContext__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./helpers */ "./app/js/chatbot/helpers.js");



// React & Vendor Libs
const {
  useState,
  useMemo,
  useRef
} = wp.element;
const ChatUploadIcon = () => {
  const css = (0,_helpers__WEBPACK_IMPORTED_MODULE_0__.useClasses)();
  const {
    state,
    actions
  } = (0,_ChatbotContext__WEBPACK_IMPORTED_MODULE_1__.useChatbotContext)();
  const {
    uploadedFile,
    busy,
    imageUpload,
    fileUpload,
    fileSearch,
    draggingType
  } = state;
  const {
    onUploadFile
  } = actions;
  const [isHovering, setIsHovering] = useState(false);
  const fileInputRef = useRef();
  const hasUploadedFile = uploadedFile === null || uploadedFile === void 0 ? void 0 : uploadedFile.uploadedId;
  const uploadEnabled = imageUpload || fileSearch || fileUpload;
  const handleMouseEnter = () => setIsHovering(true);
  const handleMouseLeave = () => setIsHovering(false);
  const resetUpload = () => onUploadFile(null);
  const handleClick = () => {
    if (uploadedFile !== null && uploadedFile !== void 0 && uploadedFile.localFile) {
      resetUpload();
      return;
    }
    if (!busy) {
      fileInputRef.current.click();
    }
  };
  const handleFileChange = event => {
    const file = event.target.files[0];
    if (file) onUploadFile(file);
  };

  // Mock uploadedFile for testing
  // const mockUploadedFile = {
  //   localFile: { type: 'image/png' },
  //   uploadedId: '123',
  //   uploadProgress: 83.39
  // };
  const file = uploadedFile;
  const type = useMemo(() => {
    if (file !== null && file !== void 0 && file.localFile) {
      return file.localFile.type.startsWith('image/') ? 'image' : 'document';
    }
    return draggingType;
  }, [file, draggingType]);
  const imgClass = useMemo(() => {
    let status = 'idle';
    if (file !== null && file !== void 0 && file.uploadProgress) {
      status = 'up';
    } else if (draggingType) {
      status = 'add';
    } else if (isHovering && hasUploadedFile) {
      status = 'del';
    } else if (hasUploadedFile) {
      status = 'ok';
    } else if (isHovering && !hasUploadedFile) {
      status = 'add';
    }
    const typeClass = type ? type.toLowerCase() : 'idle';
    return `mwai-file-upload-icon mwai-${typeClass}-${status}`;
  }, [type, file, draggingType, isHovering, hasUploadedFile]);

  // Calculate the UploadProgress Value
  const uploadProgress = useMemo(() => {
    if (file !== null && file !== void 0 && file.uploadProgress) {
      if (file.uploadProgress > 99) {
        return 99;
      }
      return Math.round(file.uploadProgress);
    }
    return "";
  }, [file]);
  return uploadEnabled ? /*#__PURE__*/React.createElement("div", {
    disabled: busy,
    onClick: handleClick,
    onMouseEnter: handleMouseEnter,
    onMouseLeave: handleMouseLeave,
    className: css('mwai-file-upload', {
      'mwai-enabled': uploadedFile === null || uploadedFile === void 0 ? void 0 : uploadedFile.uploadedId,
      'mwai-busy': (uploadedFile === null || uploadedFile === void 0 ? void 0 : uploadedFile.localFile) && !(uploadedFile !== null && uploadedFile !== void 0 && uploadedFile.uploadedId)
    }),
    style: {
      cursor: busy ? 'default' : 'pointer'
    }
  }, /*#__PURE__*/React.createElement("div", {
    className: imgClass
  }, /*#__PURE__*/React.createElement("span", {
    className: "mwai-file-upload-progress"
  }, uploadProgress)), /*#__PURE__*/React.createElement("input", {
    type: "file",
    ref: fileInputRef,
    onChange: handleFileChange,
    style: {
      display: 'none'
    }
  })) : null;
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatUploadIcon);

/***/ }),

/***/ "./app/js/chatbot/ChatbotBody.js":
/*!***************************************!*\
  !*** ./app/js/chatbot/ChatbotBody.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var markdown_to_jsx__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! markdown-to-jsx */ "./node_modules/markdown-to-jsx/dist/index.modern.js");
/* harmony import */ var _app_chatbot_ChatbotContext__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/chatbot/ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
/* harmony import */ var _ChatbotInput__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ChatbotInput */ "./app/js/chatbot/ChatbotInput.js");
/* harmony import */ var _ChatbotSubmit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ChatbotSubmit */ "./app/js/chatbot/ChatbotSubmit.js");
/* harmony import */ var _ChatUploadIcon__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./ChatUploadIcon */ "./app/js/chatbot/ChatUploadIcon.js");
/* harmony import */ var _ChatbotRealtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ChatbotRealtime */ "./app/js/chatbot/ChatbotRealtime.js");
/* harmony import */ var _ChatbotEvents__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./ChatbotEvents */ "./app/js/chatbot/ChatbotEvents.js");
const {
  useState,
  useEffect,
  useRef,
  useMemo
} = wp.element;








const markdownOptions = {
  overrides: {
    a: {
      props: {
        target: "_blank"
      }
    }
  }
};
const ChatbotBody = ({
  conversationRef,
  onScroll,
  messageList,
  jsxShortcuts,
  jsxBlocks,
  inputClassNames,
  handleDrop,
  handleDrag,
  needsFooter,
  needTools,
  uploadIconPosition
}) => {
  const {
    state,
    actions
  } = (0,_app_chatbot_ChatbotContext__WEBPACK_IMPORTED_MODULE_0__.useChatbotContext)();
  const {
    debugMode,
    eventLogs,
    messages,
    error,
    isRealtime,
    textCompliance,
    chatbotInputRef,
    isWindow
  } = state;
  const {
    resetError
  } = actions;
  const [allStreamData, setAllStreamData] = useState([]);
  const [clearedMessageIds, setClearedMessageIds] = useState(new Set());
  const streamDataRef = useRef([]);
  const lastMessageCountRef = useRef(0);
  const [realtimeMessages, setRealtimeMessages] = useState([]);

  // Reset clearedMessageIds when messages are cleared (new conversation)
  useEffect(() => {
    if (messages.length === 0 || messages.length === 1 && messages[0].role === 'assistant') {
      setClearedMessageIds(new Set());
    }
  }, [messages]);
  useEffect(() => {
    // Collect all stream events from all messages (including realtime)
    const newStreamData = [];
    const allMessages = [...messages, ...realtimeMessages];
    allMessages.forEach(message => {
      // Skip messages that have been cleared
      if (message.streamEvents && debugMode && !clearedMessageIds.has(message.id)) {
        // Add all events from this message
        message.streamEvents.forEach(event => {
          newStreamData.push({
            ...event,
            messageId: message.id
          });
        });
      }
    });

    // DON'T overwrite if we're in realtime mode - preserve directly added events
    if (!isRealtime) {
      // Always update to show all events
      streamDataRef.current = newStreamData;
      setAllStreamData(newStreamData);
    }
  }, [messages, realtimeMessages, debugMode, isRealtime, clearedMessageIds]);
  const handleClearStreamData = () => {
    // Clear the allStreamData
    setAllStreamData([]);
    streamDataRef.current = [];
    // Mark which message IDs have been cleared to prevent their events from reappearing
    const clearedMessageIds = new Set();
    [...messages, ...realtimeMessages].forEach(msg => {
      if (msg.streamEvents) {
        clearedMessageIds.add(msg.id);
      }
    });
    setClearedMessageIds(clearedMessageIds);
  };
  return /*#__PURE__*/React.createElement("div", {
    className: "mwai-body"
  }, !isRealtime && /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
    ref: conversationRef,
    className: "mwai-conversation",
    onScroll: onScroll
  }, messageList, jsxShortcuts), jsxBlocks, /*#__PURE__*/React.createElement("div", {
    className: inputClassNames,
    onClick: () => {
      var _chatbotInputRef$curr;
      return (_chatbotInputRef$curr = chatbotInputRef.current) === null || _chatbotInputRef$curr === void 0 ? void 0 : _chatbotInputRef$curr.focusInput();
    },
    onDrop: handleDrop,
    onDragEnter: event => handleDrag(event, true),
    onDragLeave: event => handleDrag(event, false),
    onDragOver: event => handleDrag(event, true)
  }, /*#__PURE__*/React.createElement(_ChatbotInput__WEBPACK_IMPORTED_MODULE_1__["default"], null), /*#__PURE__*/React.createElement(_ChatbotSubmit__WEBPACK_IMPORTED_MODULE_2__["default"], null))), isRealtime && /*#__PURE__*/React.createElement("div", {
    className: "mwai-realtime"
  }, /*#__PURE__*/React.createElement(_ChatbotRealtime__WEBPACK_IMPORTED_MODULE_3__["default"], {
    onMessagesUpdate: setRealtimeMessages,
    onStreamEvent: event => {
      setAllStreamData(prev => [...prev, event]);
    }
  })), error && /*#__PURE__*/React.createElement("div", {
    className: "mwai-error",
    onClick: () => resetError()
  }, /*#__PURE__*/React.createElement(markdown_to_jsx__WEBPACK_IMPORTED_MODULE_4__["default"], {
    options: markdownOptions
  }, error)), needsFooter && /*#__PURE__*/React.createElement("div", {
    className: "mwai-footer"
  }, needTools && /*#__PURE__*/React.createElement("div", {
    className: "mwai-tools"
  }, uploadIconPosition === 'mwai-tools' && /*#__PURE__*/React.createElement(_ChatUploadIcon__WEBPACK_IMPORTED_MODULE_5__["default"], null)), textCompliance && /*#__PURE__*/React.createElement("div", {
    className: "mwai-compliance",
    dangerouslySetInnerHTML: {
      __html: textCompliance
    }
  })), eventLogs && /*#__PURE__*/React.createElement(_ChatbotEvents__WEBPACK_IMPORTED_MODULE_6__["default"], {
    allStreamData: allStreamData,
    debugMode: debugMode,
    onClear: handleClearStreamData,
    hasData: allStreamData.length > 0,
    isWindow: isWindow
  }));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotBody);

/***/ }),

/***/ "./app/js/chatbot/ChatbotContent.js":
/*!******************************************!*\
  !*** ./app/js/chatbot/ChatbotContent.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var markdown_to_jsx__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! markdown-to-jsx */ "./node_modules/markdown-to-jsx/dist/index.modern.js");
/* harmony import */ var _app_helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/helpers */ "./app/js/helpers.js");
const {
  useMemo
} = wp.element;



// Display a clickable link with additional file information
const LinkContainer = ({
  href,
  children
}) => {
  if (!href) {
    return /*#__PURE__*/React.createElement("span", null, children);
  }
  const target = '_blank';
  const isFile = String(children) === "Uploaded File";
  if (isFile) {
    const filename = href.split('/').pop();
    return /*#__PURE__*/React.createElement("a", {
      href: href,
      target: target,
      rel: "noopener noreferrer",
      className: "mwai-filename"
    }, /*#__PURE__*/React.createElement("span", null, "\u2713 ", filename));
  }
  return /*#__PURE__*/React.createElement("a", {
    href: href,
    target: target,
    rel: "noopener noreferrer"
  }, children);
};
const ChatbotContent = ({
  message
}) => {
  let content = message.content ?? "";

  // Ensure this is enclosed markdown
  const matches = (content.match(/```/g) || []).length;
  if (matches % 2 !== 0) {
    // if count is odd
    content += "\n```"; // add ``` at the end
  }
  const markdownOptions = useMemo(() => {
    const options = {
      overrides: {
        BlinkingCursor: {
          component: _app_helpers__WEBPACK_IMPORTED_MODULE_0__.BlinkingCursor
        },
        a: {
          component: LinkContainer
        },
        // Max width for images should be 300px
        img: {
          props: {
            onError: e => {
              const src = e.target.src;
              const isImage = src.match(/\.(jpeg|jpg|gif|png)$/) !== null;
              if (isImage) {
                e.target.src = "https://placehold.co/600x200?text=Expired+Image";
                return;
              }
            },
            className: "mwai-image"
          }
        }
      }
    };
    return options;
  }, []);
  const renderedContent = useMemo(() => {
    let out = "";
    try {
      out = (0,markdown_to_jsx__WEBPACK_IMPORTED_MODULE_1__.compiler)(content, markdownOptions);
    } catch (e) {
      console.error("Crash in markdown-to-jsx! Reverting to plain text.", {
        e,
        content
      });
      out = content;
    }
    return out;
  }, [content, markdownOptions]);

  // If streaming, always show the blinking cursor
  if (message.isStreaming) {
    return /*#__PURE__*/React.createElement(React.Fragment, null, renderedContent, /*#__PURE__*/React.createElement(_app_helpers__WEBPACK_IMPORTED_MODULE_0__.BlinkingCursor, null));
  }
  return renderedContent;
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotContent);

/***/ }),

/***/ "./app/js/chatbot/ChatbotContext.js":
/*!******************************************!*\
  !*** ./app/js/chatbot/ChatbotContext.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ChatbotContextProvider: () => (/* binding */ ChatbotContextProvider),
/* harmony export */   useChatbotContext: () => (/* binding */ useChatbotContext)
/* harmony export */ });
/* harmony import */ var _app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/chatbot/helpers */ "./app/js/chatbot/helpers.js");
/* harmony import */ var _app_chatbot_MwaiAPI__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @app/chatbot/MwaiAPI */ "./app/js/chatbot/MwaiAPI.js");
/* harmony import */ var _app_helpers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @app/helpers */ "./app/js/helpers.js");
/* harmony import */ var _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @app/helpers/tokenManager */ "./app/js/helpers/tokenManager.js");
// React & Vendor Libs
const {
  useContext,
  createContext,
  useState,
  useMemo,
  useEffect,
  useCallback,
  useRef
} = wp.element;

// AI Engine





const rawAiName = 'AI: ';
const rawUserName = 'User: ';
const ChatbotContext = createContext();
const useChatbotContext = () => {
  const context = useContext(ChatbotContext);
  if (!context) {
    throw new Error('useChatbotContext must be used within a ChatbotContextProvider');
  }
  return context;
};
const ChatbotContextProvider = ({
  children,
  ...rest
}) => {
  var _params$startSentence;
  const {
    params,
    system,
    theme,
    atts
  } = rest;
  const {
    timeElapsed,
    startChrono,
    stopChrono
  } = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.useChrono)();
  const shortcodeStyles = useMemo(() => (theme === null || theme === void 0 ? void 0 : theme.settings) || {}, [theme]);
  const [restNonce, setRestNonce] = useState(system.restNonce || _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_1__["default"].getToken());
  const restNonceRef = useRef(system.restNonce || _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_1__["default"].getToken());

  // Subscribe to global token updates
  useEffect(() => {
    const unsubscribe = _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_1__["default"].subscribe(newToken => {
      setRestNonce(newToken);
      restNonceRef.current = newToken;
    });
    return unsubscribe;
  }, []);
  const [messages, setMessages] = useState([]);
  const [shortcuts, setShortcuts] = useState([]);
  const [blocks, setBlocks] = useState([]);
  const [locked, setLocked] = useState(false);
  const [chatId, setChatId] = useState((0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.randomStr)());
  const [inputText, setInputText] = useState('');
  const [chatbotTriggered, setChatbotTriggered] = useState(false);
  const [showIconMessage, setShowIconMessage] = useState(false);
  const [uploadedFile, setUploadedFile] = useState({
    localFile: null,
    uploadedId: null,
    uploadedUrl: null,
    uploadProgress: null
  });
  const [windowed, setWindowed] = useState(true); // When fullscreen is enabled, minimize is the reduced version.
  const [open, setOpen] = useState(false);
  const [error, setError] = useState(null);
  const [busy, setBusy] = useState(false);
  const [busyNonce, setBusyNonce] = useState(false);
  const [serverReply, setServerReply] = useState();
  const [previousResponseId, setPreviousResponseId] = useState(null);
  const chatbotInputRef = useRef();
  const conversationRef = useRef();
  const hasFocusRef = useRef(false);
  const {
    isListening,
    setIsListening,
    speechRecognitionAvailable
  } = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.useSpeechRecognition)(text => {
    setInputText(text);
  });

  // System Parameters
  //const id = system.id;
  const stream = system.stream || false;
  const internalId = useMemo(() => (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.randomStr)(), []);
  const botId = system.botId;
  const customId = system.customId;
  const userData = system.userData;
  const [sessionId, setSessionId] = useState(system.sessionId);
  const contextId = system.contextId; // This is used by Content Aware (to retrieve a Post)
  const pluginUrl = system.pluginUrl;
  const restUrl = system.restUrl;
  const debugMode = system.debugMode;
  const eventLogs = system.eventLogs;
  const virtualKeyboardFix = system.virtual_keyboard_fix;
  const typewriter = (system === null || system === void 0 ? void 0 : system.typewriter) ?? false;
  const speechRecognition = (system === null || system === void 0 ? void 0 : system.speech_recognition) ?? false;
  const speechSynthesis = (system === null || system === void 0 ? void 0 : system.speech_synthesis) ?? false;
  const startSentence = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.doPlaceholders)(((_params$startSentence = params.startSentence) === null || _params$startSentence === void 0 ? void 0 : _params$startSentence.trim()) ?? "", userData);

  // Initial Actions, Shortcuts, and Blocks
  const initialActions = system.actions || [];
  const initialShortcuts = system.shortcuts || [];
  const initialBlocks = system.blocks || [];

  // UI Parameters
  const isMobile = document.innerWidth <= 768;
  const processedParams = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.processParameters)(params, userData);
  const {
    aiName,
    userName,
    guestName,
    aiAvatar,
    userAvatar,
    guestAvatar
  } = processedParams;
  const {
    textSend,
    textClear,
    textInputMaxLength,
    textInputPlaceholder,
    textCompliance,
    window: isWindow,
    copyButton,
    headerSubtitle,
    fullscreen,
    localMemory: localMemoryParam,
    icon,
    iconText,
    iconTextDelay,
    iconAlt,
    iconPosition,
    iconBubble,
    imageUpload,
    fileUpload,
    fileSearch
  } = processedParams;
  const isRealtime = processedParams.mode === 'realtime';
  const localMemory = localMemoryParam && (!!customId || !!botId);
  const localStorageKey = localMemory ? `mwai-chat-${customId || botId}` : null;
  const {
    cssVariables,
    iconUrl,
    aiAvatarUrl,
    userAvatarUrl,
    guestAvatarUrl
  } = useMemo(() => {
    const processUrl = url => {
      if (!url) return null;
      if ((0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.isEmoji)(url)) return url;
      return (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.isURL)(url) ? url : `${pluginUrl}/images/${url}`;
    };
    const iconUrl = icon ? processUrl(icon) : `${pluginUrl}/images/chat-traditional-1.svg`;
    const finalAiAvatarUrl = processUrl(processedParams.aiAvatarUrl);
    const finalUserAvatarUrl = processUrl(processedParams.userAvatarUrl);
    const finalGuestAvatarUrl = processUrl(processedParams.guestAvatarUrl);
    const cssVariables = Object.keys(shortcodeStyles).reduce((acc, key) => {
      acc[`--mwai-${key}`] = shortcodeStyles[key];
      return acc;
    }, {});
    return {
      cssVariables,
      iconUrl,
      aiAvatarUrl: finalAiAvatarUrl,
      userAvatarUrl: finalUserAvatarUrl,
      guestAvatarUrl: finalGuestAvatarUrl
    };
  }, [icon, pluginUrl, shortcodeStyles, processedParams]);
  const [draggingType, setDraggingType] = useState(false);
  // This is used to block the drop event when the file is not allowed:
  const [isBlocked, setIsBlocked] = useState(false);

  // Theme-Related Parameters
  const uploadIconPosition = useMemo(() => {
    if ((theme === null || theme === void 0 ? void 0 : theme.themeId) === 'timeless') {
      return 'mwai-tools';
    }
    return "mwai-input";
  }, [theme === null || theme === void 0 ? void 0 : theme.themeId]);
  const submitButtonConf = useMemo(() => {
    return {
      text: textSend,
      textSend: textSend,
      textClear: textClear,
      imageSend: (theme === null || theme === void 0 ? void 0 : theme.themeId) === 'timeless' ? pluginUrl + '/images/action-submit-blue.svg' : null,
      imageClear: (theme === null || theme === void 0 ? void 0 : theme.themeId) === 'timeless' ? pluginUrl + '/images/action-clear-blue.svg' : null
      //imageOnly: false,
    };
  }, [pluginUrl, textClear, textSend, theme === null || theme === void 0 ? void 0 : theme.themeId]);
  const resetMessages = () => {
    resetUploadedFile();
    setPreviousResponseId(null); // Reset response ID when clearing messages
    if (startSentence) {
      const freshMessages = [{
        id: (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.randomStr)(),
        role: 'assistant',
        content: startSentence,
        who: rawAiName,
        timestamp: new Date().getTime()
      }];
      setMessages(freshMessages);
    } else {
      setMessages([]);
    }
  };
  const refreshRestNonce = useCallback(async (force = false) => {
    try {
      if (!force && restNonce) {
        return restNonce;
      }
      setBusyNonce(true);
      const res = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiFetch)(`${restUrl}/mwai/v1/start_session`);
      const data = await res.json();
      setRestNonce(data.restNonce);
      restNonceRef.current = data.restNonce;
      _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_1__["default"].setToken(data.restNonce); // Update globally
      // Update sessionId if it was N/A or different
      if (data.sessionId && data.sessionId !== 'N/A') {
        setSessionId(data.sessionId);
      }

      // Also update if new_token is present (in case of token test mode)
      if (data.new_token) {
        // Log token update with expiration info
        if (data.token_expires_at) {
          const expiresAt = new Date(data.token_expires_at * 1000);
          console.log(`[MWAI] 🔐 New token received - expires at ${expiresAt.toLocaleTimeString()} (in ${data.token_expires_in}s)`);
        }
        setRestNonce(data.new_token);
        restNonceRef.current = data.new_token;
        _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_1__["default"].setToken(data.new_token); // Update globally
        return data.new_token;
      }
      return data.restNonce;
    } catch (err) {
      console.error('Error while fetching the restNonce.', err);
    } finally {
      setBusyNonce(false);
    }
  }, [restNonce, setRestNonce, restUrl, setSessionId]);

  // Track if we're resuming an existing conversation
  const [isResumingConversation, setIsResumingConversation] = useState(false);
  const [isConversationLoaded, setIsConversationLoaded] = useState(false);

  // Initialize the initialActions, initialShortcuts, and initialBlocks
  useEffect(() => {
    if (debugMode) {
      // console.log('[INIT] Shortcuts init effect', {
      //   isConversationLoaded,
      //   isResumingConversation,
      //   messagesLength: messages.length,
      //   initialShortcutsLength: initialShortcuts.length
      // });
    }

    // Wait until we've checked for existing conversation before initializing
    if (!isConversationLoaded) {
      return;
    }

    // Only show initial shortcuts if this is a new conversation
    // Check both isResumingConversation flag and if we have existing messages (excluding start sentence)
    const hasExistingConversation = isResumingConversation || messages.length > 1 || messages.length === 1 && messages[0].content !== startSentence;
    if (!hasExistingConversation) {
      if (debugMode) {
        // console.log('[INIT] Showing initial shortcuts');
      }
      if (initialActions.length > 0) {
        handleActions(initialActions);
      }
      if (initialShortcuts.length > 0) {
        handleShortcuts(initialShortcuts);
      }
      if (initialBlocks.length > 0) {
        handleBlocks(initialBlocks);
      }
    } else {
      if (debugMode) {
        // console.log('[INIT] NOT showing initial shortcuts - existing conversation');
      }
    }
  }, [isConversationLoaded, isResumingConversation, messages, startSentence]);

  // Initialized the restNonce
  useEffect(() => {
    if (chatbotTriggered && !restNonce) {
      refreshRestNonce();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [chatbotTriggered]);
  useEffect(() => {
    if (inputText.length > 0 && !chatbotTriggered) {
      setChatbotTriggered(true);
    }
  }, [chatbotTriggered, inputText]);

  // Reset messages when the start sentence changes.
  useEffect(() => {
    resetMessages();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [startSentence]);

  // Initializes the mwaiAPI (used to interact with the chatbot)
  useEffect(() => {
    if (customId || botId) {
      const existingChatbotIndex = _app_chatbot_MwaiAPI__WEBPACK_IMPORTED_MODULE_3__.mwaiAPI.chatbots.findIndex(chatbot => chatbot.internalId === internalId);
      const chatbot = {
        internalId: internalId,
        // This is used to identify the chatbot in the current page.
        botId: botId,
        chatId: chatId,
        customId: customId,
        localStorageKey: localStorageKey,
        // Add localStorageKey for discussion loading
        open: () => {
          setTasks(prevTasks => [...prevTasks, {
            action: 'open'
          }]);
        },
        close: () => {
          setTasks(prevTasks => [...prevTasks, {
            action: 'close'
          }]);
        },
        clear: params => {
          const {
            chatId = null
          } = params || {};
          setTasks(prevTasks => [...prevTasks, {
            action: 'clear',
            data: {
              chatId
            }
          }]);
        },
        toggle: () => {
          setTasks(prevTasks => [...prevTasks, {
            action: 'toggle'
          }]);
        },
        ask: (text, submit = false) => {
          setTasks(prevTasks => [...prevTasks, {
            action: 'ask',
            data: {
              text,
              submit
            }
          }]);
        },
        lock: () => {
          setLocked(true);
        },
        unlock: () => {
          setLocked(false);
        },
        setShortcuts: shortcuts => {
          setTasks(prevTasks => [...prevTasks, {
            action: 'setShortcuts',
            data: shortcuts
          }]);
        },
        setBlocks: blocks => {
          setTasks(prevTasks => [...prevTasks, {
            action: 'setBlocks',
            data: blocks
          }]);
        },
        addBlock: block => {
          setTasks(prevTasks => [...prevTasks, {
            action: 'addBlock',
            data: block
          }]);
        },
        removeBlockById: blockId => {
          setTasks(prevTasks => [...prevTasks, {
            action: 'removeBlockById',
            data: blockId
          }]);
        },
        getBlocks: () => {
          return blocks;
        },
        setContext: ({
          chatId,
          messages,
          previousResponseId
        }) => {
          console.warn('MwaiAPI: setContext is deprecated. Please use setConversation instead.');
          setTasks(prevTasks => [...prevTasks, {
            action: 'setContext',
            data: {
              chatId,
              messages,
              previousResponseId
            }
          }]);
        },
        setConversation: ({
          chatId,
          messages,
          previousResponseId
        }) => {
          setTasks(prevTasks => [...prevTasks, {
            action: 'setContext',
            data: {
              chatId,
              messages,
              previousResponseId
            }
          }]);
        }
      };
      if (existingChatbotIndex !== -1) {
        _app_chatbot_MwaiAPI__WEBPACK_IMPORTED_MODULE_3__.mwaiAPI.chatbots[existingChatbotIndex] = chatbot;
      } else {
        _app_chatbot_MwaiAPI__WEBPACK_IMPORTED_MODULE_3__.mwaiAPI.chatbots.push(chatbot);
      }
    }
  }, [botId, chatId, customId, internalId, localStorageKey, blocks]);

  // Starts the timer when the chatbot is busy
  useEffect(() => {
    var _chatbotInputRef$curr;
    if (busy) {
      startChrono();
      return;
    }
    if (!isMobile && hasFocusRef.current && (_chatbotInputRef$curr = chatbotInputRef.current) !== null && _chatbotInputRef$curr !== void 0 && _chatbotInputRef$curr.focusInput) {
      chatbotInputRef.current.focusInput();
    }
    stopChrono();
  }, [busy, startChrono, stopChrono, isMobile]);
  const saveMessages = useCallback(messages => {
    if (!localStorageKey) {
      return;
    }
    localStorage.setItem(localStorageKey, (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.nekoStringify)({
      chatId: chatId,
      messages: messages
    }));
  }, [localStorageKey, chatId]);
  const resetError = () => {
    setError(null);
  };

  // New BotId: Initializes the chat history
  useEffect(() => {
    let chatHistory = [];
    if (localStorageKey) {
      chatHistory = localStorage.getItem(localStorageKey);
      if (chatHistory) {
        chatHistory = JSON.parse(chatHistory);
        setMessages(chatHistory.messages);
        setChatId(chatHistory.chatId);
        setIsResumingConversation(true);
        setIsConversationLoaded(true);
        return;
      }
    }
    setIsResumingConversation(false);
    setIsConversationLoaded(true);
    resetMessages();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [botId]);

  // Track executed actions to prevent double execution
  const executedActionsRef = useRef(new Set());
  const handleActions = useCallback((actions, lastMessage) => {
    actions = actions || [];
    let callsCount = 0;
    for (const action of actions) {
      if (action.type === 'function') {
        const data = action.data || {};
        const {
          name = null,
          args = []
        } = data;

        // Create a unique key for this action based on function name and arguments
        const actionKey = `${name}_${JSON.stringify(args)}`;

        // Check if this action was already executed recently
        if (executedActionsRef.current.has(actionKey)) {
          if (debugMode) {
            console.log(`[CHATBOT] Skipping duplicate execution of ${name}`);
          }
          continue;
        }
        const finalArgs = args ? Object.values(args).map(arg => {
          return JSON.stringify(arg);
        }) : [];
        try {
          if (debugMode) {
            // eslint-disable-next-line no-console
            console.log(`[CHATBOT] CALL ${name}(${finalArgs.join(', ')})`);
          }

          // Mark as executed before calling to prevent race conditions
          executedActionsRef.current.add(actionKey);
          eval(`${name}(${finalArgs.join(', ')})`);
          callsCount++;

          // Clean up old entries after 5 seconds
          setTimeout(() => {
            executedActionsRef.current.delete(actionKey);
          }, 5000);
        } catch (err) {
          console.error('Error while executing an action.', err);
          // Remove from executed set if there was an error
          executedActionsRef.current.delete(actionKey);
        }
      }
    }
    if (!lastMessage.content && callsCount > 0) {
      lastMessage.content = `*Done!*`;
    }
  }, [debugMode]);
  const handleShortcuts = useCallback(shortcuts => {
    setShortcuts(shortcuts || []);
  }, []);
  const handleBlocks = useCallback(blocks => {
    setBlocks(blocks || []);
  }, []);

  // New Server Reply: Update the messages
  useEffect(() => {
    if (!serverReply) {
      return;
    }
    setBusy(false);
    const freshMessages = [...messages];
    const lastMessage = freshMessages.length > 0 ? freshMessages[freshMessages.length - 1] : null;

    // Failure
    if (!serverReply.success) {
      // Get the user message before removing it to restore in input field
      let userMessage = null;

      // Remove the isQuerying placeholder for the assistant.
      if (lastMessage.role === 'assistant' && lastMessage.isQuerying) {
        freshMessages.pop();
      }

      // Get the user message before removing it
      const userMessageIndex = freshMessages.length - 1;
      if (userMessageIndex >= 0 && freshMessages[userMessageIndex].role === 'user') {
        userMessage = freshMessages[userMessageIndex];
        // Extract the actual text content without image/file markdown
        const content = userMessage.content;
        // Remove markdown image/file prefix if present
        const markdownMatch = content.match(/^(?:\!\[.*?\]\(.*?\)|\[.*?\]\(.*?\))\n(.*)$/s);
        const textToRestore = markdownMatch ? markdownMatch[1] : content;
        // Restore the user's text in the input field
        setInputText(textToRestore);
      }

      // Remove the user message.
      freshMessages.pop();

      // Add error message
      freshMessages.push({
        id: (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.randomStr)(),
        role: 'system',
        content: serverReply.message,
        who: rawAiName,
        timestamp: new Date().getTime()
      });
      setMessages(freshMessages);
      saveMessages(freshMessages);
      return;
    }

    // Success: Let's update the isQuerying/isStreaming or add a new message.
    if (lastMessage.role === 'assistant' && lastMessage.isQuerying) {
      lastMessage.content = (0,_app_chatbot_MwaiAPI__WEBPACK_IMPORTED_MODULE_3__.applyFilters)('ai.reply', serverReply.reply, {
        chatId,
        botId
      });
      if (serverReply.images) {
        lastMessage.images = serverReply.images;
      }
      lastMessage.timestamp = new Date().getTime();
      delete lastMessage.isQuerying;
      handleActions(serverReply === null || serverReply === void 0 ? void 0 : serverReply.actions, lastMessage);
      handleBlocks(serverReply === null || serverReply === void 0 ? void 0 : serverReply.blocks);
      handleShortcuts(serverReply === null || serverReply === void 0 ? void 0 : serverReply.shortcuts);
    } else if (lastMessage.role === 'assistant' && lastMessage.isStreaming) {
      lastMessage.content = (0,_app_chatbot_MwaiAPI__WEBPACK_IMPORTED_MODULE_3__.applyFilters)('ai.reply', serverReply.reply, {
        chatId,
        botId
      });
      if (serverReply.images) {
        lastMessage.images = serverReply.images;
      }
      lastMessage.timestamp = new Date().getTime();
      delete lastMessage.isStreaming;
      // Add completion event for streaming
      if (debugMode && lastMessage.streamEvents) {
        var _lastMessage$streamEv;
        const now = new Date().getTime();
        const startTime = ((_lastMessage$streamEv = lastMessage.streamEvents[0]) === null || _lastMessage$streamEv === void 0 ? void 0 : _lastMessage$streamEv.timestamp) || now;
        const duration = now - startTime;

        // Format duration in human-readable format
        let durationText;
        if (duration < 1000) {
          durationText = `${duration}ms`;
        } else if (duration < 60000) {
          durationText = `${(duration / 1000).toFixed(1)}s`;
        } else {
          const minutes = Math.floor(duration / 60000);
          const seconds = (duration % 60000 / 1000).toFixed(0);
          durationText = `${minutes}m ${seconds}s`;
        }
        lastMessage.streamEvents.push({
          type: 'event',
          subtype: 'status',
          data: `Request completed in ${durationText}.`,
          timestamp: now
        });
      }
      handleActions(serverReply === null || serverReply === void 0 ? void 0 : serverReply.actions, lastMessage);
      handleBlocks(serverReply === null || serverReply === void 0 ? void 0 : serverReply.blocks);
      handleShortcuts(serverReply === null || serverReply === void 0 ? void 0 : serverReply.shortcuts);
    }
    // Otherwise, let's add a new message
    else {
      const newMessage = {
        id: (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.randomStr)(),
        role: 'assistant',
        content: (0,_app_chatbot_MwaiAPI__WEBPACK_IMPORTED_MODULE_3__.applyFilters)('ai.reply', serverReply.reply, {
          botId,
          chatId,
          customId
        }),
        who: rawAiName,
        timestamp: new Date().getTime()
      };
      if (serverReply.images) {
        newMessage.images = serverReply.images;
      }
      handleActions(serverReply === null || serverReply === void 0 ? void 0 : serverReply.actions, newMessage);
      handleBlocks(serverReply === null || serverReply === void 0 ? void 0 : serverReply.blocks);
      handleShortcuts(serverReply === null || serverReply === void 0 ? void 0 : serverReply.shortcuts);
      freshMessages.push(newMessage);
    }

    // Store response ID if available (for Responses API)
    if (serverReply.responseId) {
      setPreviousResponseId(serverReply.responseId);
    }
    setMessages(freshMessages);
    saveMessages(freshMessages);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [serverReply]);

  // #region Submit Actions (Clear, Submit, File Upload, etc.)
  const onClear = useCallback(async ({
    chatId = null
  } = {}) => {
    if (!chatId) {
      chatId = (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.randomStr)();
    }
    await setChatId(chatId);
    if (localStorageKey) {
      localStorage.removeItem(localStorageKey);
    }
    resetMessages();
    setInputText('');
    // Mark as not resuming since we're starting fresh
    setIsResumingConversation(false);
    setIsConversationLoaded(true);
    // Restore initial shortcuts instead of clearing them
    if (initialShortcuts.length > 0) {
      handleShortcuts(initialShortcuts);
    } else {
      setShortcuts([]);
    }
    setBlocks([]);
    setPreviousResponseId(null); // Reset response ID on clear
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [botId, initialShortcuts, handleShortcuts]);
  const onStartRealtimeSession = useCallback(async () => {
    const body = {
      botId: botId,
      customId: customId,
      contextId: contextId,
      chatId: chatId
    };
    const nonce = restNonceRef.current ?? (await refreshRestNonce());
    const res = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiFetch)(`${restUrl}/mwai-ui/v1/openai/realtime/start`, body, nonce);
    const data = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiHandleRes)(res, null, null, null, debugMode);
    return data;
  }, [botId, customId, contextId, chatId, restNonce, refreshRestNonce, restUrl]);
  const onCommitStats = useCallback(async (stats, refId = null) => {
    try {
      const nonce = restNonceRef.current ?? (await refreshRestNonce());
      const res = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiFetch)(`${restUrl}/mwai-ui/v1/openai/realtime/stats`, {
        botId: botId,
        session: sessionId,
        refId: refId || chatId,
        stats: stats
      }, nonce);
      const data = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiHandleRes)(res, null, null, null, debugMode);
      return {
        success: data.success,
        message: data.message,
        overLimit: data.overLimit || false,
        limitMessage: data.limitMessage || null
      };
    } catch (err) {
      console.error('Error while committing stats.', err);
      return {
        success: false,
        message: 'An error occurred while committing the stats.'
      };
    }
  }, [botId, restNonce, refreshRestNonce, restUrl, sessionId, chatId]);
  const onCommitDiscussions = useCallback(async (messages = []) => {
    try {
      const nonce = restNonceRef.current ?? (await refreshRestNonce());
      const payload = {
        botId: botId,
        session: sessionId,
        chatId: chatId,
        messages: messages ?? []
      };
      const res = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiFetch)(`${restUrl}/mwai-ui/v1/openai/realtime/discussions`, payload, nonce);
      const data = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiHandleRes)(res, null, null, null, debugMode);
      return {
        success: data.success,
        message: data.message
      };
    } catch (err) {
      console.error('Error while committing discussion.', err);
      return {
        success: false,
        message: 'An error occurred while committing the discussion.'
      };
    }
  }, [botId, chatId, restNonce, refreshRestNonce, restUrl, sessionId]);
  const onRealtimeFunctionCallback = useCallback(async (functionId, functionType, functionName, functionTarget, args) => {
    const body = {
      functionId,
      functionType,
      functionName,
      functionTarget,
      arguments: args
    };
    if (functionTarget === 'js') {
      const finalArgs = args ? Object.values(args).map(arg => {
        return JSON.stringify(arg);
      }) : [];
      try {
        if (debugMode) {
          // eslint-disable-next-line no-console
          console.log(`[CHATBOT] CALL ${functionName}(${finalArgs.join(', ')})`);
        }
        eval(`${functionName}(${finalArgs.join(', ')})`);
        return {
          success: true,
          message: 'The function was executed',
          data: null
        };
      } catch (err) {
        console.error('Error while executing an action.', err);
        return {
          success: false,
          message: 'An error occurred while executing the function.',
          data: null
        };
      }
    } else {
      const nonce = restNonceRef.current ?? (await refreshRestNonce());
      const res = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiFetch)(`${restUrl}/mwai-ui/v1/openai/realtime/call`, body, nonce);
      const data = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiHandleRes)(res, null, null, null, debugMode);
      return data;
    }
    return null;
  }, [restNonce, refreshRestNonce, restUrl, debugMode]);
  const onSubmit = useCallback(async textQuery => {
    var _uploadedFile$localFi;
    if (busy) {
      console.error('AI Engine: There is already a query in progress.');
      return;
    }

    // This avoid the onSubmit to send an event.
    if (typeof textQuery !== 'string') {
      textQuery = inputText;
    }
    const currentFile = uploadedFile;
    const currentImageUrl = uploadedFile === null || uploadedFile === void 0 ? void 0 : uploadedFile.uploadedUrl;
    const mimeType = uploadedFile === null || uploadedFile === void 0 || (_uploadedFile$localFi = uploadedFile.localFile) === null || _uploadedFile$localFi === void 0 ? void 0 : _uploadedFile$localFi.type;
    const isImage = mimeType ? mimeType.startsWith('image') : false;

    // textQuery is the text that will be sent to AI
    // but we also need the text that will be displayed in the chat, with the uploaded image first, using Markdown
    let textDisplay = textQuery;
    if (currentImageUrl) {
      if (isImage) {
        textDisplay = `![Uploaded Image](${currentImageUrl})\n${textQuery}`;
      } else {
        textDisplay = `[Uploaded File](${currentImageUrl})\n${textQuery}`;
      }
    }
    setBusy(true);
    setInputText('');
    setShortcuts([]);
    setBlocks([]);
    resetUploadedFile();
    const bodyMessages = [...messages, {
      id: (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.randomStr)(),
      role: 'user',
      content: textDisplay,
      who: rawUserName,
      timestamp: new Date().getTime()
    }];
    saveMessages(bodyMessages);
    const freshMessageId = (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.randomStr)();
    const freshMessages = [...bodyMessages, {
      id: freshMessageId,
      role: 'assistant',
      content: stream ? '' : null,
      who: rawAiName,
      timestamp: null,
      isQuerying: stream ? false : true,
      isStreaming: stream ? true : false,
      // Add initial stream event for request start
      streamEvents: stream && debugMode ? [] : undefined
    }];
    setMessages(freshMessages);
    const body = {
      botId: botId,
      customId: customId,
      session: sessionId,
      chatId: chatId,
      contextId: contextId,
      messages: messages,
      newMessage: textQuery,
      newFileId: currentFile === null || currentFile === void 0 ? void 0 : currentFile.uploadedId,
      stream,
      ...atts
    };

    // Add previousResponseId if available (for Responses API)
    if (previousResponseId) {
      body.previousResponseId = previousResponseId;
    }
    try {
      if (debugMode) {
        // eslint-disable-next-line no-console
        console.log('[CHATBOT] OUT: ', body);
      }
      const streamCallback = !stream ? null : (content, streamData) => {
        // Debug enhanced streaming data
        if (debugMode && streamData && streamData.subtype) {
          console.log('[CHATBOT] STREAM EVENT:', streamData);
        }
        setMessages(messages => {
          const freshMessages = [...messages];
          const lastMessage = freshMessages.length > 0 ? freshMessages[freshMessages.length - 1] : null;
          if (lastMessage && lastMessage.id === freshMessageId) {
            lastMessage.content = content;
            lastMessage.timestamp = new Date().getTime();
            // Store stream data for enhanced display
            if (streamData && streamData.subtype) {
              // Initialize streamEvents array if not exists
              if (!lastMessage.streamEvents) {
                lastMessage.streamEvents = [];
              }
              // Add the new event with timestamp
              lastMessage.streamEvents.push({
                ...streamData,
                timestamp: new Date().getTime()
              });
            }
          }
          return freshMessages;
        });
      };

      // We need to refresh the restNonce before sending the request.
      const nonce = restNonceRef.current ?? (await refreshRestNonce());

      // Send "Request sent..." event immediately when we send the HTTP request
      if (stream && debugMode && streamCallback) {
        streamCallback('', {
          type: 'event',
          subtype: 'status',
          data: 'Request sent...',
          timestamp: new Date().getTime()
        });
      }

      // Handler for token updates
      const handleTokenUpdate = newToken => {
        setRestNonce(newToken);
        restNonceRef.current = newToken;
        _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_1__["default"].setToken(newToken); // Update globally
      };

      // Let's perform the request. The mwaiHandleRes will handle the complexity of response.
      const res = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiFetch)(`${restUrl}/mwai-ui/v1/chats/submit`, body, nonce, stream, undefined, handleTokenUpdate);
      const data = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiHandleRes)(res, streamCallback, debugMode ? "CHATBOT" : null, handleTokenUpdate, debugMode);
      if (!data.success && data.message) {
        setError(data.message);
        // We remove the two last messages (the query, and the 'busy' message).
        const updatedMessages = [...freshMessages];
        updatedMessages.pop(); // Remove assistant message

        // Get the user message before removing it to restore in input field
        const userMessageIndex = updatedMessages.length - 1;
        if (userMessageIndex >= 0 && updatedMessages[userMessageIndex].role === 'user') {
          const userMessage = updatedMessages[userMessageIndex];
          // Extract the actual text content without image/file markdown
          const content = userMessage.content;
          // Remove markdown image/file prefix if present
          const markdownMatch = content.match(/^(?:\!\[.*?\]\(.*?\)|\[.*?\]\(.*?\))\n(.*)$/s);
          const textToRestore = markdownMatch ? markdownMatch[1] : content;
          // Restore the user's text in the input field
          setInputText(textToRestore);
        }
        updatedMessages.pop(); // Remove user message

        setMessages(updatedMessages);
        saveMessages(updatedMessages);
        setBusy(false);
        return;
      }
      setServerReply(data);
    } catch (err) {
      console.error("An error happened in the handling of the chatbot response.", {
        err
      });
      setBusy(false);
      setError(err.message || 'An error occurred while processing your request. Please try again.');

      // Remove the "thinking" message that was added
      setMessages(prevMessages => {
        const lastMessage = prevMessages[prevMessages.length - 1];
        if (lastMessage && lastMessage.role === 'assistant' && lastMessage.content === '') {
          return prevMessages.slice(0, -1);
        }
        return prevMessages;
      });
    }
  }, [busy, uploadedFile, messages, saveMessages, stream, botId, customId, sessionId, chatId, contextId, atts, inputText, debugMode, restNonce, refreshRestNonce, restUrl]);
  const onSubmitAction = useCallback((forcedText = null) => {
    var _chatbotInputRef$curr2;
    const hasFileUploaded = !!(uploadedFile !== null && uploadedFile !== void 0 && uploadedFile.uploadedId);
    hasFocusRef.current = ((_chatbotInputRef$curr2 = chatbotInputRef.current) === null || _chatbotInputRef$curr2 === void 0 ? void 0 : _chatbotInputRef$curr2.currentElement) && document.activeElement === chatbotInputRef.current.currentElement();
    if (forcedText) {
      onSubmit(forcedText);
    } else if (hasFileUploaded || inputText.length > 0) {
      onSubmit(inputText);
    }
  }, [inputText, onSubmit, uploadedFile === null || uploadedFile === void 0 ? void 0 : uploadedFile.uploadedId]);

  // This is called when the user uploads an image or file.
  const onFileUpload = async (file, type = "N/A", purpose = "N/A") => {
    try {
      if (file === null) {
        resetUploadedFile();
        return;
      }
      const params = {
        type,
        purpose
      };
      const url = `${restUrl}/mwai-ui/v1/files/upload`;

      // Upload with progress
      const nonce = restNonceRef.current ?? (await refreshRestNonce());
      const res = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_2__.mwaiFetchUpload)(url, file, nonce, progress => {
        setUploadedFile({
          localFile: file,
          uploadedId: null,
          uploadedUrl: null,
          uploadProgress: progress
        });
      }, params);
      setUploadedFile({
        localFile: file,
        uploadedId: res.data.id,
        uploadedUrl: res.data.url,
        uploadProgress: null
      });
    } catch (error) {
      console.error('onFileUpload Error', error);
      setError(error.message || 'An unknown error occurred');
      resetUploadedFile();
    }
  };

  // This is called when the user uploads an image or file.
  // TODO: Why don't we have the resetError in onFileUpload and remove this function?
  const onUploadFile = async file => {
    if (error) {
      resetError();
    }
    return onFileUpload(file);
  };
  const resetUploadedFile = () => {
    setUploadedFile({
      localFile: null,
      uploadedId: null,
      uploadedUrl: null,
      uploadProgress: null
    });
  };
  // #endregion

  // #region Timer
  const runTimer = useCallback(() => {
    const timer = setTimeout(() => {
      setOpen(prevOpen => {
        if (!prevOpen) {
          setShowIconMessage(true);
        }
        return prevOpen;
      });
    }, iconTextDelay * 1000);
    return () => clearTimeout(timer);
  }, [iconText, iconTextDelay]);
  useEffect(() => {
    if (iconText && !iconTextDelay) {
      setShowIconMessage(true);
    } else if (iconText && iconTextDelay) {
      return runTimer();
    }
  }, [iconText]);
  // #endregion

  // #region Tasks Queue
  const [tasks, setTasks] = useState([]);
  const runTasks = useCallback(async () => {
    if (tasks.length > 0) {
      const task = tasks[0];
      if (task.action === 'ask') {
        const {
          text,
          submit
        } = task.data;
        if (submit) {
          onSubmit(text);
        } else {
          setInputText(text);
        }
      } else if (task.action === 'toggle') {
        setOpen(prevOpen => !prevOpen);
      } else if (task.action === 'open') {
        setOpen(true);
      } else if (task.action === 'close') {
        setOpen(false);
      } else if (task.action === 'clear') {
        const {
          chatId
        } = task.data;
        onClear({
          chatId
        });
      } else if (task.action === 'setContext') {
        const {
          chatId,
          messages,
          previousResponseId
        } = task.data;
        setChatId(chatId);
        setMessages(messages);
        if (previousResponseId) {
          setPreviousResponseId(previousResponseId);
        }
        // Mark as resuming conversation when loading from Discussions Module
        setIsResumingConversation(true);
        setIsConversationLoaded(true);
        // Clear shortcuts when loading an existing discussion
        setShortcuts([]);
        // Save to localStorage to persist the loaded conversation
        saveMessages(messages);
      } else if (task.action === 'setShortcuts') {
        const shortcuts = task.data;
        handleShortcuts(shortcuts);
      } else if (task.action === 'setBlocks') {
        const blocks = task.data;
        handleBlocks(blocks);
      } else if (task.action === 'addBlock') {
        const block = task.data;
        setBlocks(prevBlocks => {
          return [...prevBlocks, block];
        });
      } else if (task.action === 'removeBlockById') {
        const blockId = task.data;
        setBlocks(prevBlocks => {
          return prevBlocks.filter(block => block.id !== blockId);
        });
      }
      setTasks(prevTasks => prevTasks.slice(1));
    }
  }, [tasks, onClear, onSubmit, setChatId, setInputText, setMessages, setOpen, handleShortcuts, handleBlocks]);
  useEffect(() => {
    runTasks();
  }, [runTasks]);
  // #endregion

  const actions = {
    // Text Chatbot
    setInputText,
    saveMessages,
    setMessages,
    resetMessages,
    setError,
    resetError,
    onClear,
    onSubmit,
    onSubmitAction,
    onFileUpload,
    onUploadFile,
    setOpen,
    setWindowed,
    setShowIconMessage,
    setIsListening,
    setDraggingType,
    setIsBlocked,
    // Realtime Chatbot
    onStartRealtimeSession,
    onRealtimeFunctionCallback,
    onCommitStats,
    onCommitDiscussions
  };
  const state = {
    theme,
    botId,
    customId,
    userData,
    pluginUrl,
    inputText,
    messages,
    shortcuts,
    // Quick actions are buttons that can be displayed in the chat.
    blocks,
    // Blocks are used to display HTML content. A form, a video, etc.
    busy,
    error,
    setBusy,
    typewriter,
    speechRecognition,
    speechSynthesis,
    virtualKeyboardFix,
    localMemory,
    isRealtime,
    imageUpload,
    fileUpload,
    uploadedFile,
    fileSearch,
    textSend,
    textClear,
    textInputMaxLength,
    textInputPlaceholder,
    textCompliance,
    aiName,
    userName,
    guestName,
    aiAvatar,
    userAvatar,
    guestAvatar,
    aiAvatarUrl,
    userAvatarUrl,
    guestAvatarUrl,
    isWindow,
    copyButton,
    headerSubtitle,
    fullscreen,
    icon,
    iconText,
    iconAlt,
    iconPosition,
    iconBubble,
    cssVariables,
    iconUrl,
    chatbotInputRef,
    conversationRef,
    isMobile,
    open,
    locked,
    windowed,
    showIconMessage,
    timeElapsed,
    isListening,
    speechRecognitionAvailable,
    uploadIconPosition,
    submitButtonConf,
    draggingType,
    isBlocked,
    busyNonce,
    debugMode,
    eventLogs,
    system // Add the full system object
  };
  return /*#__PURE__*/React.createElement(ChatbotContext.Provider, {
    value: {
      state,
      actions
    }
  }, children);
};

/***/ }),

/***/ "./app/js/chatbot/ChatbotEvents.js":
/*!*****************************************!*\
  !*** ./app/js/chatbot/ChatbotEvents.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/wrench.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/activity.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/brain.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/search.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/circle-alert.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/x.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/minimize-2.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/maximize-2.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/chevron-down.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/chevron-right.js");
/* harmony import */ var markdown_to_jsx__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! markdown-to-jsx */ "./node_modules/markdown-to-jsx/dist/index.modern.js");
const {
  useState,
  useMemo,
  useEffect
} = wp.element;


const ChatbotEvents = ({
  allStreamData,
  debugMode,
  onClear,
  hasData,
  isWindow
}) => {
  const [expanded, setExpanded] = useState({});
  const [isVisible, setIsVisible] = useState(false); // Default to hidden/minimized
  const [showAll, setShowAll] = useState(isWindow ? false : true); // Minimal view (false) in popup mode, full view otherwise

  // Process all stream data into chunks
  const chunks = useMemo(() => {
    if (!allStreamData || allStreamData.length === 0) {
      return [];
    }
    const processedData = allStreamData.map((data, index) => ({
      ...data,
      id: `${data.messageId}-${index}`,
      displayTime: new Date(data.timestamp).toLocaleTimeString('en-US', {
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      })
    })).reverse(); // Newest first

    // If showAll is false, only show the last event
    if (!showAll) {
      return processedData.slice(0, 1);
    }

    // Otherwise show up to 20 events
    return processedData.slice(0, 20);
  }, [allStreamData, showAll]);

  // Map subtypes to simplified categories
  const getEventCategory = (subtype, metadata) => {
    switch (subtype) {
      case 'tool_call':
      case 'tool_args':
        return 'function';
      case 'tool_result':
        // Check if this is an MCP result by looking at the metadata
        if ((metadata === null || metadata === void 0 ? void 0 : metadata.is_mcp) === true) {
          return 'mcp';
        }
        // Legacy check: look for previous mcp_tool_call event
        if (metadata !== null && metadata !== void 0 && metadata.tool_name && chunks.some(c => {
          var _c$metadata;
          return c.subtype === 'mcp_tool_call' && ((_c$metadata = c.metadata) === null || _c$metadata === void 0 ? void 0 : _c$metadata.name) === metadata.tool_name;
        })) {
          return 'mcp';
        }
        return 'function';
      case 'mcp_discovery':
      case 'mcp_tool_call':
      case 'mcp_tool_result':
        return 'mcp';
      case 'thinking':
        return 'thinking';
      case 'status':
        return 'output';
      // Most status events are about output
      case 'web_search':
      case 'file_search':
        return 'search';
      case 'error':
        return 'error';
      case 'warning':
        return 'warning';
      case 'content':
        return 'output';
      default:
        return subtype;
    }
  };
  const getIcon = category => {
    switch (category) {
      case 'function':
        return /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_0__["default"], {
          size: 14
        });
      case 'mcp':
        return /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_1__["default"], {
          size: 14
        });
      case 'thinking':
        return /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_2__["default"], {
          size: 14
        });
      case 'output':
        return /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_1__["default"], {
          size: 14
        });
      case 'search':
        return /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_3__["default"], {
          size: 14
        });
      case 'error':
      case 'warning':
        return /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_4__["default"], {
          size: 14
        });
      default:
        return /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_1__["default"], {
          size: 14
        });
    }
  };
  const getCategoryColor = (category, data) => {
    switch (category) {
      case 'function':
        return '#3b82f6';
      // blue for function calls
      case 'mcp':
        return '#8b5cf6';
      // purple for MCP discovery
      case 'thinking':
        return '#8b5cf6';
      // purple
      case 'output':
        // Different colors for different output messages
        if (data && data.includes('completed')) {
          return '#10b981'; // green for completion
        }
        if (data && data.includes('started') || data && data.includes('...')) {
          return '#06b6d4'; // cyan for in-progress
        }
        return '#6b7280';
      // gray for other output
      case 'search':
        return '#f59e0b';
      // amber
      case 'error':
        return '#ef4444';
      // red
      case 'warning':
        return '#f97316';
      // orange
      default:
        return '#6b7280';
      // gray
    }
  };
  const toggleExpanded = chunkId => {
    setExpanded(prev => ({
      ...prev,
      [chunkId]: !prev[chunkId]
    }));
  };

  // Get the latest meaningful event for status display
  const latestEvent = useMemo(() => {
    if (chunks.length === 0) return null;

    // Look for the most recent meaningful status
    for (const chunk of chunks) {
      const category = getEventCategory(chunk.subtype, chunk.metadata);

      // Show all events except debug and heartbeat
      if (chunk.subtype !== 'debug' && chunk.subtype !== 'heartbeat') {
        // For completed events, check if it's the stream completed message
        if (chunk.data.includes('Stream completed')) {
          // Find if there's a request completed message after this
          const completedIndex = chunks.findIndex(c => c.data.includes('Request completed'));
          if (completedIndex >= 0 && completedIndex < chunks.indexOf(chunk)) {
            // Use the request completed message instead
            const completedChunk = chunks[completedIndex];
            return {
              data: completedChunk.data,
              category: getEventCategory(completedChunk.subtype, completedChunk.metadata),
              color: getCategoryColor(getEventCategory(completedChunk.subtype, completedChunk.metadata), completedChunk.data)
            };
          }
        }
        return {
          data: chunk.data,
          category: category,
          color: getCategoryColor(category, chunk.data)
        };
      }
    }
    return null;
  }, [chunks]);
  return /*#__PURE__*/React.createElement("div", {
    className: `mwai-chunks ${!isVisible ? 'mwai-chunks-collapsed' : ''}`
  }, /*#__PURE__*/React.createElement("div", {
    className: "mwai-chunks-header"
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_1__["default"], {
    size: 12
  }), /*#__PURE__*/React.createElement("span", {
    className: "mwai-chunks-title"
  }, "Events", latestEvent && /*#__PURE__*/React.createElement("span", {
    className: "mwai-chunks-status",
    style: {
      color: latestEvent.color
    }
  }, ": ", latestEvent.data)), isVisible && /*#__PURE__*/React.createElement(React.Fragment, null, chunks.length > 0 && onClear && /*#__PURE__*/React.createElement("div", {
    className: "mwai-chunks-toggle",
    onClick: onClear,
    title: "Clear stream events"
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_5__["default"], {
    size: 12
  })), !isWindow && /*#__PURE__*/React.createElement("div", {
    className: "mwai-chunks-toggle",
    onClick: () => setShowAll(!showAll),
    title: showAll ? "Show minimal (last event only)" : "Show detailed (all events)"
  }, showAll ? /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_6__["default"], {
    size: 12
  }) : /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_7__["default"], {
    size: 12
  }))), /*#__PURE__*/React.createElement("div", {
    className: "mwai-chunks-toggle",
    onClick: () => setIsVisible(!isVisible),
    title: isVisible ? "Hide events" : "Show events"
  }, isVisible ? /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_8__["default"], {
    size: 12
  }) : /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_9__["default"], {
    size: 12
  }))), isVisible && (chunks.length === 0 ? /*#__PURE__*/React.createElement("div", {
    className: "mwai-chunk"
  }, /*#__PURE__*/React.createElement("div", {
    className: "mwai-chunk-header"
  }, /*#__PURE__*/React.createElement("span", {
    className: "mwai-chunk-time"
  }, "--:--:--"), /*#__PURE__*/React.createElement("span", {
    className: "mwai-chunk-type",
    style: {
      backgroundColor: '#6b7280'
    }
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_1__["default"], {
    size: 14
  }), "waiting"), /*#__PURE__*/React.createElement("span", {
    className: "mwai-chunk-data"
  }, "No events yet."))) : chunks.map(chunk => {
    const isExpanded = expanded[chunk.id];
    const category = getEventCategory(chunk.subtype, chunk.metadata);
    const hasDetails = chunk.metadata && Object.keys(chunk.metadata).length > 0 || category === 'thinking';
    return /*#__PURE__*/React.createElement("div", {
      key: chunk.id,
      className: "mwai-chunk"
    }, /*#__PURE__*/React.createElement("div", {
      className: "mwai-chunk-header",
      onClick: () => hasDetails && toggleExpanded(chunk.id)
    }, /*#__PURE__*/React.createElement("span", {
      className: "mwai-chunk-time"
    }, chunk.displayTime), /*#__PURE__*/React.createElement("span", {
      className: "mwai-chunk-type",
      style: {
        backgroundColor: getCategoryColor(category, chunk.data)
      }
    }, getIcon(category), category), /*#__PURE__*/React.createElement("span", {
      className: "mwai-chunk-data"
    }, (() => {
      const dataStr = typeof chunk.data === 'string' ? chunk.data : JSON.stringify(chunk.data);
      if (category === 'thinking') {
        // Check if the content starts with **something**
        const headerMatch = dataStr.match(/^\*\*([^*]+)\*\*/);
        if (headerMatch) {
          // Show only the header text (no formatting)
          return headerMatch[1];
        }

        // If no header found, show the beginning of the content
        return dataStr.substring(0, 50) + (dataStr.length > 50 ? '...' : '');
      }
      return dataStr;
    })()), hasDetails && /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_9__["default"], {
      size: 12,
      className: "mwai-chunk-expand",
      style: {
        transform: isExpanded ? 'rotate(90deg)' : 'none'
      }
    })), isExpanded && hasDetails && /*#__PURE__*/React.createElement("div", {
      className: "mwai-chunk-details"
    }, category === 'thinking' ? /*#__PURE__*/React.createElement("div", {
      style: {
        padding: '0px 10px',
        fontFamily: 'system-ui'
      }
    }, (() => {
      const dataStr = typeof chunk.data === 'string' ? chunk.data : JSON.stringify(chunk.data);
      try {
        return (0,markdown_to_jsx__WEBPACK_IMPORTED_MODULE_10__.compiler)(dataStr);
      } catch (e) {
        return /*#__PURE__*/React.createElement("pre", null, dataStr);
      }
    })()) : /*#__PURE__*/React.createElement("pre", null, JSON.stringify(chunk.metadata, null, 2))));
  })));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotEvents);

/***/ }),

/***/ "./app/js/chatbot/ChatbotHeader.js":
/*!*****************************************!*\
  !*** ./app/js/chatbot/ChatbotHeader.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _app_helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @app/helpers */ "./app/js/helpers.js");
/* harmony import */ var _ChatbotContext__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./helpers */ "./app/js/chatbot/helpers.js");
// React & Vendor Libs
const {
  useMemo
} = wp.element;



function formatAvatar(aiName, pluginUrl, iconUrl, aiAvatarUrl) {
  const getAvatarSrc = url => {
    if ((0,_helpers__WEBPACK_IMPORTED_MODULE_0__.isURL)(url)) {
      return url;
    } else if (url) {
      return `${pluginUrl}/images/${url}`;
    }
    return null;
  };
  const renderAvatar = (src, alt) => /*#__PURE__*/React.createElement("div", {
    className: "mwai-avatar"
  }, /*#__PURE__*/React.createElement("img", {
    alt: alt,
    src: src
  }));
  const renderEmoji = emoji => /*#__PURE__*/React.createElement("div", {
    className: "mwai-avatar mwai-emoji",
    style: {
      fontSize: '48px',
      lineHeight: '48px'
    }
  }, emoji);
  if ((0,_app_helpers__WEBPACK_IMPORTED_MODULE_1__.isEmoji)(aiAvatarUrl || iconUrl)) {
    return renderEmoji(aiAvatarUrl || iconUrl);
  }

  // Priority: aiAvatarUrl > iconUrl > default image
  const avatarSrc = getAvatarSrc(aiAvatarUrl) || iconUrl || `${pluginUrl}/images/chat-openai.svg`;
  if (avatarSrc) {
    return renderAvatar(avatarSrc, "AI Engine");
  }

  // If no avatar is available, return the aiName as text
  return /*#__PURE__*/React.createElement("div", {
    className: "mwai-name-text"
  }, aiName);
}
const ChatbotHeader = () => {
  const {
    state,
    actions
  } = (0,_ChatbotContext__WEBPACK_IMPORTED_MODULE_2__.useChatbotContext)();
  const {
    theme,
    isWindow,
    fullscreen,
    aiName,
    pluginUrl,
    open,
    iconUrl,
    aiAvatarUrl,
    windowed,
    headerSubtitle
  } = state;
  const {
    setOpen,
    setWindowed
  } = actions;
  const headerContent = useMemo(() => {
    if (!isWindow) {
      return null;
    }
    const timelessStyle = (theme === null || theme === void 0 ? void 0 : theme.themeId) === 'timeless';
    const avatarImage = timelessStyle ? formatAvatar(aiName, pluginUrl, iconUrl, aiAvatarUrl) : null;
    const finalHeaderSubtitle = headerSubtitle === null || headerSubtitle === undefined ? "Discuss with" : headerSubtitle;
    return /*#__PURE__*/React.createElement(React.Fragment, null, timelessStyle && /*#__PURE__*/React.createElement(React.Fragment, null, avatarImage, /*#__PURE__*/React.createElement("div", {
      className: "mwai-name"
    }, finalHeaderSubtitle && /*#__PURE__*/React.createElement("small", {
      className: "mwai-subtitle"
    }, finalHeaderSubtitle), /*#__PURE__*/React.createElement("div", null, aiName)), /*#__PURE__*/React.createElement("div", {
      style: {
        flex: 'auto'
      }
    })), /*#__PURE__*/React.createElement("div", {
      className: "mwai-buttons"
    }, fullscreen && /*#__PURE__*/React.createElement("div", {
      className: "mwai-resize-button",
      onClick: () => setWindowed(!windowed)
    }), /*#__PURE__*/React.createElement("div", {
      className: "mwai-close-button",
      onClick: () => setOpen(!open)
    })));
  }, [isWindow, theme === null || theme === void 0 ? void 0 : theme.themeId, aiName, pluginUrl, iconUrl, aiAvatarUrl, fullscreen, setWindowed, windowed, setOpen, open, headerSubtitle]);
  return /*#__PURE__*/React.createElement("div", {
    className: "mwai-header"
  }, headerContent);
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotHeader);

/***/ }),

/***/ "./app/js/chatbot/ChatbotInput.js":
/*!****************************************!*\
  !*** ./app/js/chatbot/ChatbotInput.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react_textarea_autosize__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react-textarea-autosize */ "./node_modules/react-textarea-autosize/dist/react-textarea-autosize.browser.development.esm.js");
/* harmony import */ var _app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/chatbot/helpers */ "./app/js/chatbot/helpers.js");
/* harmony import */ var _ChatUploadIcon__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ChatUploadIcon */ "./app/js/chatbot/ChatUploadIcon.js");
/* harmony import */ var _ChatbotContext__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
// React & Vendor Libs
const {
  useRef,
  useState,
  useEffect,
  useImperativeHandle
} = wp.element;




const ChatbotInput = () => {
  const css = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.useClasses)();
  const {
    state,
    actions
  } = (0,_ChatbotContext__WEBPACK_IMPORTED_MODULE_1__.useChatbotContext)();
  const {
    inputText,
    textInputMaxLength,
    textInputPlaceholder,
    error,
    speechRecognitionAvailable,
    isMobile,
    conversationRef,
    open,
    uploadIconPosition,
    locked,
    isListening,
    busy,
    speechRecognition,
    chatbotInputRef
  } = state;
  const {
    onSubmitAction,
    setIsListening,
    resetError,
    setInputText
  } = actions;
  const [composing, setComposing] = useState(false);
  const inputRef = useRef();
  useImperativeHandle(chatbotInputRef, () => ({
    focusInput: () => {
      var _inputRef$current;
      (_inputRef$current = inputRef.current) === null || _inputRef$current === void 0 || _inputRef$current.focus();
    },
    currentElement: () => inputRef.current
  }));

  // Focus input when opening (except mobile)
  useEffect(() => {
    if (!isMobile && open) {
      inputRef.current.focus();
    }
    if (conversationRef.current) {
      conversationRef.current.scrollTop = conversationRef.current.scrollHeight;
    }
  }, [open, isMobile, conversationRef]);
  const onTypeText = text => {
    if (isListening) {
      setIsListening(false);
    }
    if (error) {
      resetError();
    }
    setInputText(text);
  };
  const classNames = css('mwai-input-text', {});
  return /*#__PURE__*/React.createElement("div", {
    ref: chatbotInputRef,
    className: classNames
  }, uploadIconPosition === 'mwai-input' && /*#__PURE__*/React.createElement(_ChatUploadIcon__WEBPACK_IMPORTED_MODULE_2__["default"], null), /*#__PURE__*/React.createElement(react_textarea_autosize__WEBPACK_IMPORTED_MODULE_3__["default"], {
    ref: inputRef,
    disabled: busy || locked,
    placeholder: textInputPlaceholder,
    value: inputText,
    maxLength: textInputMaxLength,
    onCompositionStart: () => setComposing(true),
    onCompositionEnd: () => setComposing(false),
    onKeyDown: event => {
      if (composing) return;
      if (event.code === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        onSubmitAction();
      }
    },
    onChange: e => onTypeText(e.target.value)
  }), speechRecognition && /*#__PURE__*/React.createElement(_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.Microphone, {
    active: isListening,
    disabled: !speechRecognitionAvailable || busy,
    className: "mwai-microphone",
    onClick: () => setIsListening(!isListening)
  }));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotInput);

/***/ }),

/***/ "./app/js/chatbot/ChatbotName.js":
/*!***************************************!*\
  !*** ./app/js/chatbot/ChatbotName.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _ChatbotContext__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./helpers */ "./app/js/chatbot/helpers.js");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../helpers */ "./app/js/helpers.js");
// React & Vendor Libs
const {
  useMemo
} = wp.element;



const ChatbotName = ({
  role = 'user'
}) => {
  const {
    state
  } = (0,_ChatbotContext__WEBPACK_IMPORTED_MODULE_0__.useChatbotContext)();
  const {
    pluginUrl,
    iconUrl,
    userData,
    userName,
    aiName,
    guestName,
    userAvatar,
    aiAvatar,
    guestAvatar,
    userAvatarUrl,
    aiAvatarUrl,
    guestAvatarUrl
  } = state;
  const formattedOutput = useMemo(() => {
    const isAi = role === 'assistant';
    const isGuest = !userData && !isAi;
    const getAvatarSrc = (url, isUserData = false) => {
      if ((0,_helpers__WEBPACK_IMPORTED_MODULE_1__.isURL)(url)) {
        return url;
      } else if (url && !(0,_helpers__WEBPACK_IMPORTED_MODULE_2__.isEmoji)(url)) {
        return isUserData ? url : `${pluginUrl}/images/${url}`;
      }
      if (!isUserData && !(0,_helpers__WEBPACK_IMPORTED_MODULE_2__.isEmoji)(url)) {
        console.warn('Invalid URL for avatar:', url);
      }
      return null;
    };
    const renderAvatar = (src, alt) => /*#__PURE__*/React.createElement("div", {
      className: "mwai-avatar"
    }, /*#__PURE__*/React.createElement("img", {
      width: "32",
      height: "32",
      src: src,
      alt: alt
    }));
    const renderEmoji = emoji => /*#__PURE__*/React.createElement("div", {
      className: "mwai-avatar mwai-emoji",
      style: {
        fontSize: '32px',
        lineHeight: '32px'
      }
    }, emoji);
    const renderName = name => /*#__PURE__*/React.createElement("div", {
      className: "mwai-name-text"
    }, name);
    const getAvatarContent = (avatarEnabled, avatarUrl, fallbackUrl, altText, isUserData = false) => {
      if (!avatarEnabled) return null;
      if ((0,_helpers__WEBPACK_IMPORTED_MODULE_2__.isEmoji)(avatarUrl)) {
        return renderEmoji(avatarUrl);
      }
      const src = getAvatarSrc(avatarUrl, isUserData) || fallbackUrl;
      if (!src) return null;
      return renderAvatar(src, altText);
    };
    if (isAi) {
      const aiAvatarContent = getAvatarContent(aiAvatar, aiAvatarUrl, iconUrl, "AI Avatar");
      if (aiAvatarContent) {
        if (aiAvatarUrl === null && iconUrl) {
          console.warn('Using iconUrl as a temporary fallback for AI avatar. Please set aiAvatarUrl.');
        }
        return aiAvatarContent;
      }
      return renderName(aiName);
    }
    if (!isGuest) {
      const userAvatarContent = getAvatarContent(userAvatar, userAvatarUrl, userData === null || userData === void 0 ? void 0 : userData.AVATAR_URL, "User Avatar", true);
      if (userAvatarContent) return userAvatarContent;
      return renderName(formatName(userName, guestName, userData));
    }
    if (isGuest) {
      const guestAvatarContent = getAvatarContent(guestAvatar, guestAvatarUrl, null, "Guest Avatar");
      if (guestAvatarContent) return guestAvatarContent;
      return renderName(guestName || "Guest");
    }
  }, [role, aiName, userName, guestName, userData, iconUrl, aiAvatar, userAvatar, guestAvatar, aiAvatarUrl, userAvatarUrl, guestAvatarUrl, pluginUrl]);
  return /*#__PURE__*/React.createElement("span", {
    className: "mwai-name"
  }, formattedOutput);
};
function formatName(template, guestName, userData) {
  if (!userData || Object.keys(userData).length === 0) {
    return guestName || template || "Guest";
  }
  return Object.entries(userData).reduce((acc, [placeholder, value]) => {
    const realPlaceholder = `{${placeholder}}`;
    return acc.includes(realPlaceholder) ? acc.replace(realPlaceholder, value) : acc;
  }, template);
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotName);

/***/ }),

/***/ "./app/js/chatbot/ChatbotRealtime.js":
/*!*******************************************!*\
  !*** ./app/js/chatbot/ChatbotRealtime.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/play.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/loader.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/square.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/pause.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/users.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/captions.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/bug.js");
/* harmony import */ var _ChatbotContext__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
/* harmony import */ var _AudioVisualizer__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./AudioVisualizer */ "./app/js/chatbot/AudioVisualizer.js");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./helpers */ "./app/js/chatbot/helpers.js");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../helpers */ "./app/js/helpers.js");
/* harmony import */ var _helpers_RealtimeEventEmitter__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../helpers/RealtimeEventEmitter */ "./app/js/helpers/RealtimeEventEmitter.js");
/* harmony import */ var _constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../constants/streamTypes */ "./app/js/constants/streamTypes.js");
// React & Vendor Libs
const {
  useState,
  useRef,
  useCallback,
  useMemo,
  useEffect
} = wp.element;








// Debug levels
const DEBUG_LEVELS = {
  none: 0,
  low: 1,
  normal: 2,
  high: 3,
  verbose: 4
};
const CURRENT_DEBUG = DEBUG_LEVELS.low;

/** Only logs if CURRENT_DEBUG >= level. */
function debugLog(level, ...args) {
  if (CURRENT_DEBUG >= level) console.log(...args);
}

/** Simplified parseUsage to gather 6 fields. */
function parseUsage(usage) {
  if (!usage) return null;
  const {
    input_token_details: {
      text_tokens: textIn = 0,
      audio_tokens: audioIn = 0,
      cached_tokens_details: {
        text_tokens: cachedText = 0,
        audio_tokens: cachedAudio = 0
      } = {}
    } = {},
    output_token_details: {
      text_tokens: textOut = 0,
      audio_tokens: audioOut = 0
    } = {}
  } = usage;
  return {
    text_input_tokens: textIn,
    audio_input_tokens: audioIn,
    text_output_tokens: textOut,
    audio_output_tokens: audioOut,
    text_cached_tokens: cachedText,
    audio_cached_tokens: cachedAudio
  };
}

/**
 * Representation of a chatbot participant (user or assistant).
 */
function getChatbotRepresentation(state, role = 'user') {
  const {
    pluginUrl,
    iconUrl,
    userData,
    userName,
    aiName,
    guestName,
    userAvatar,
    aiAvatar,
    guestAvatar,
    userAvatarUrl,
    aiAvatarUrl,
    guestAvatarUrl
  } = state;
  const getAvatarSrc = (url, isUserData = false) => {
    if ((0,_helpers__WEBPACK_IMPORTED_MODULE_0__.isURL)(url)) return url;
    if (url && !(0,_helpers__WEBPACK_IMPORTED_MODULE_1__.isEmoji)(url)) return isUserData ? url : `${pluginUrl}/images/${url}`;
    return null;
  };
  const getRepresentation = (name, avatarEnabled, avatarUrl, fallbackUrl, isUserData = false) => {
    if (avatarEnabled) {
      const src = getAvatarSrc(avatarUrl, isUserData) || fallbackUrl;
      if (src) return {
        emoji: null,
        text: null,
        image: src,
        use: 'image'
      };
    }
    if ((0,_helpers__WEBPACK_IMPORTED_MODULE_1__.isEmoji)(name)) return {
      emoji: name,
      text: null,
      image: null,
      use: 'emoji'
    };
    return {
      emoji: null,
      text: name,
      image: null,
      use: 'text'
    };
  };
  if (role === 'assistant') {
    return getRepresentation(aiName, aiAvatar, aiAvatarUrl, iconUrl);
  }
  if (userData) {
    const name = formatName(userName, guestName, userData);
    return getRepresentation(name, userAvatar, userAvatarUrl, userData === null || userData === void 0 ? void 0 : userData.AVATAR_URL, true);
  }
  if (!userData && role === 'user') {
    return getRepresentation(guestName || 'Guest', guestAvatar, guestAvatarUrl, null);
  }
  return {
    emoji: null,
    text: 'Unknown',
    image: null,
    use: 'text'
  };
}
function formatName(template, guestName, userData) {
  if (!userData || Object.keys(userData).length === 0) return guestName || template || 'Guest';
  return Object.entries(userData).reduce((acc, [key, val]) => {
    const placeholder = `{${key}}`;
    return acc.includes(placeholder) ? acc.replace(placeholder, val) : acc;
  }, template);
}
const ChatbotRealtime = ({
  onMessagesUpdate,
  onStreamEvent
}) => {
  const {
    state,
    actions
  } = (0,_ChatbotContext__WEBPACK_IMPORTED_MODULE_2__.useChatbotContext)();
  const {
    busy,
    locked,
    open,
    popup,
    system
  } = state;
  const {
    onStartRealtimeSession,
    onRealtimeFunctionCallback,
    onCommitStats,
    onCommitDiscussions,
    setError
  } = actions;
  const debugMode = (system === null || system === void 0 ? void 0 : system.debugMode) || false;
  const eventLogs = (system === null || system === void 0 ? void 0 : system.eventLogs) || false;

  // Realtime session states
  const [isConnecting, setIsConnecting] = useState(false);
  const [isSessionActive, setIsSessionActive] = useState(false);
  const [isPaused, setIsPaused] = useState(false);
  const [sessionId, setSessionId] = useState(null);
  const [whoIsSpeaking, setWhoIsSpeaking] = useState(null);

  // Statistics
  const [statistics, setStatistics] = useState({
    text_input_tokens: 0,
    audio_input_tokens: 0,
    text_output_tokens: 0,
    audio_output_tokens: 0,
    text_cached_tokens: 0,
    audio_cached_tokens: 0
  });

  // Conversation messages
  const [messages, setMessages] = useState([]);
  const processedItemIdsRef = useRef(new Set());

  // Event callback for stream events - send directly to parent
  const handleStreamEvent = useCallback((content, eventData) => {
    if (eventData && eventData.subtype && onStreamEvent) {
      // Send event directly to parent component
      onStreamEvent({
        ...eventData,
        timestamp: eventData.timestamp || new Date().getTime(),
        messageId: 'realtime-session'
      });
    }
  }, [onStreamEvent]);

  // Initialize event emitter
  const eventEmitterRef = useRef(null);
  useEffect(() => {
    eventEmitterRef.current = new _helpers_RealtimeEventEmitter__WEBPACK_IMPORTED_MODULE_3__["default"](handleStreamEvent, eventLogs);
  }, [handleStreamEvent, eventLogs]);

  // WebRTC references
  const pcRef = useRef(null);
  const dataChannelRef = useRef(null);
  const localStreamRef = useRef(null);
  const stopRealtimeConnectionRef = useRef(null);

  // Toggles
  const [showOptions, setShowOptions] = useState(true);
  const [showUsers, setShowUsers] = useState(true);
  const [showCaptions, setShowCaptions] = useState(false);
  const [showStatistics, setShowStatistics] = useState(false);

  // Assistant stream
  const [assistantStream, setAssistantStream] = useState(null);

  // Function callbacks from the server
  const functionCallbacksRef = useRef([]);

  // UI representation
  const userUI = useMemo(() => getChatbotRepresentation(state, 'user'), [state]);
  const assistantUI = useMemo(() => getChatbotRepresentation(state, 'assistant'), [state]);

  // Cleanup
  useEffect(() => {
    if (!open && isSessionActive && popup) stopRealtimeConnection();
  }, [open, popup, isSessionActive]);

  // Update parent component with messages
  useEffect(() => {
    if (onMessagesUpdate) {
      onMessagesUpdate(messages);
    }
  }, [messages, onMessagesUpdate]);

  /**
   * Helper: Send updated stats to your WordPress REST API.
   */
  const commitStatsToServer = useCallback(async usageStats => {
    // usageStats contains {text_input_tokens, audio_input_tokens, text_output_tokens, ...}
    const result = await onCommitStats(usageStats);

    // Check if user has exceeded limits
    if (result.overLimit) {
      // Emit an event about the limit being exceeded
      if (eventLogs && eventEmitterRef.current) {
        eventEmitterRef.current.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__.STREAM_TYPES.ERROR, result.limitMessage || 'Usage limit exceeded', {
          visibility: 'visible',
          error: true
        });
      }

      // Stop the realtime connection
      console.warn('Usage limit exceeded, stopping realtime connection:', result.limitMessage);
      if (stopRealtimeConnectionRef.current) {
        stopRealtimeConnectionRef.current();
      }
    }
  }, [onCommitStats, eventLogs]);

  /**
   * Enable audio transcription (Whisper).
   */
  const enableAudioTranscription = useCallback(() => {
    if (!dataChannelRef.current || dataChannelRef.current.readyState !== 'open') {
      console.error('Data channel is not open yet; cannot enable transcription.');
      return;
    }
    dataChannelRef.current.send(JSON.stringify({
      type: 'session.update',
      session: {
        input_audio_transcription: {
          model: 'whisper-1'
        }
      }
    }));
    debugLog(DEBUG_LEVELS.low, 'Sent session.update to enable Whisper.');
  }, []);

  /**
   * Handle real-time function calls from the AI.
   */
  const handleFunctionCall = useCallback(async (callId, functionName, rawArgs) => {
    let parsedArgs = {};
    try {
      parsedArgs = JSON.parse(rawArgs || '{}');
    } catch (err) {
      console.error('Could not parse function arguments.', rawArgs);
    }
    const fns = functionCallbacksRef.current;
    const cb = fns.find(f => f.name === functionName);
    if (!cb) {
      console.error(`No match for callback: '${functionName}'.`);
      return;
    }
    try {
      var _dataChannelRef$curre;
      const result = await onRealtimeFunctionCallback(cb.id, cb.type, cb.name, cb.target, parsedArgs);
      if (!(result !== null && result !== void 0 && result.success)) {
        console.error('Callback failed.', result === null || result === void 0 ? void 0 : result.message);
        // Function error will be shown in stream events
        return;
      }
      const functionOutput = result.data;

      // Emit function result event
      if (eventLogs && eventEmitterRef.current) {
        const resultPreview = typeof functionOutput === 'string' ? functionOutput : JSON.stringify(functionOutput);
        const previewText = resultPreview.length > 100 ? resultPreview.substring(0, 100) + '...' : resultPreview;
        eventEmitterRef.current.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__.STREAM_TYPES.TOOL_RESULT, `Got result from ${functionName}.`, {
          metadata: {
            tool_name: functionName,
            result: previewText,
            call_id: callId
          }
        });
      }
      if (((_dataChannelRef$curre = dataChannelRef.current) === null || _dataChannelRef$curre === void 0 ? void 0 : _dataChannelRef$curre.readyState) === 'open') {
        debugLog(DEBUG_LEVELS.low, 'Send callback value:', functionOutput);
        dataChannelRef.current.send(JSON.stringify({
          type: 'conversation.item.create',
          item: {
            type: 'function_call_output',
            call_id: callId,
            output: JSON.stringify(functionOutput)
          }
        }));
        dataChannelRef.current.send(JSON.stringify({
          type: 'response.create',
          response: {
            instructions: "Reply based on the function's output."
          }
        }));
      }
    } catch (err) {
      console.error('Error in handleFunctionCall.', err);
    }
  }, [onRealtimeFunctionCallback, eventLogs]);

  /**
   * Start the Realtime connection.
   */
  const startRealtimeConnection = useCallback(async (clientSecret, model) => {
    setIsConnecting(true);

    // Emit session starting event
    if (eventLogs && eventEmitterRef.current) {
      eventEmitterRef.current.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__.STREAM_TYPES.STATUS, 'Starting realtime session...', {
        visibility: 'visible'
      });
    }
    const pc = new RTCPeerConnection();
    pcRef.current = pc;
    let ms;
    try {
      // Check if mediaDevices API is available
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        throw new Error('MediaDevices API not available. Please ensure you are using HTTPS and a modern browser.');
      }
      ms = await navigator.mediaDevices.getUserMedia({
        audio: true
      });
      localStreamRef.current = ms;
      ms.getTracks().forEach(track => pc.addTrack(track, ms));
    } catch (err) {
      console.error('Error accessing microphone.', err);

      // Emit error event
      if (eventLogs && eventEmitterRef.current) {
        eventEmitterRef.current.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__.STREAM_TYPES.STATUS, 'Failed to access microphone: ' + err.message, {
          visibility: 'visible',
          error: true
        });
      }

      // Show error to user
      setError('Failed to access microphone. Please ensure microphone permissions are granted and try again.');
      setIsConnecting(false);
      return;
    }
    pc.ontrack = event => {
      const audioEl = document.getElementById('mwai-audio');
      if (audioEl) audioEl.srcObject = event.streams[0];
      setAssistantStream(event.streams[0]);
    };
    const dataChannel = pc.createDataChannel('oai-events');
    dataChannelRef.current = dataChannel;
    dataChannel.addEventListener('open', () => {
      debugLog(DEBUG_LEVELS.low, 'Data channel open.');

      // Emit session connected event
      if (eventLogs && eventEmitterRef.current) {
        eventEmitterRef.current.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__.STREAM_TYPES.STATUS, 'Realtime session connected', {
          visibility: 'visible'
        });
      }
      enableAudioTranscription();
    });
    dataChannel.addEventListener('message', e => {
      let msg;
      try {
        msg = JSON.parse(e.data);
      } catch (err) {
        console.error('Could not parse Realtime message.', e.data);
        return;
      }
      if (CURRENT_DEBUG >= DEBUG_LEVELS.high) {
        console.log('Incoming message from Realtime API.', msg);
      } else if (CURRENT_DEBUG === DEBUG_LEVELS.low) {
        var _msg$type;
        const isMajor = ((_msg$type = msg.type) === null || _msg$type === void 0 ? void 0 : _msg$type.endsWith('.done')) || msg.type === 'input_audio_buffer.committed' || msg.type === 'conversation.item.input_audio_transcription.completed' || msg.type === 'response.done';
        if (isMajor) console.log('Key event from Realtime API.', msg);
      }

      // Emit stream event for important realtime API events only
      if (eventLogs && msg.type && eventEmitterRef.current) {
        let eventMessage = '';
        let eventSubtype = _constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__.STREAM_TYPES.STATUS;
        let shouldEmit = false;

        // Only emit events for key moments
        switch (msg.type) {
          case 'input_audio_buffer.speech_started':
            eventMessage = 'User started talking...';
            shouldEmit = true;
            break;
          case 'input_audio_buffer.speech_stopped':
            eventMessage = 'User stopped speaking.';
            shouldEmit = true;
            break;
          case 'response.audio.started':
            eventMessage = 'Assistant started speaking.';
            shouldEmit = true;
            break;
          case 'response.audio.done':
            eventMessage = 'Assistant stopped speaking.';
            shouldEmit = true;
            break;
          case 'conversation.item.input_audio_transcription.completed':
            eventMessage = 'Got transcript from user.';
            eventSubtype = _constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__.STREAM_TYPES.TRANSCRIPT;
            shouldEmit = true;
            break;
          case 'response.audio_transcript.done':
            eventMessage = 'Got transcript from assistant.';
            eventSubtype = _constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__.STREAM_TYPES.TRANSCRIPT;
            shouldEmit = true;
            break;
          case 'response.function_call_arguments.done':
            eventMessage = `Calling ${msg.name}...`;
            eventSubtype = _constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__.STREAM_TYPES.TOOL_CALL;
            shouldEmit = true;
            break;
          case 'response.done':
            // Don't emit this event - it's too verbose
            break;
        }
        if (shouldEmit) {
          eventEmitterRef.current.emit(eventSubtype, eventMessage, {
            visibility: 'visible',
            metadata: {
              event_type: msg.type,
              event_id: msg.event_id
            }
          });
        }
      }
      switch (msg.type) {
        case 'input_audio_buffer.committed':
          {
            const itemId = msg.item_id;
            if (!processedItemIdsRef.current.has(itemId)) {
              processedItemIdsRef.current.add(itemId);
              setMessages(prev => [...prev, {
                id: itemId,
                role: 'user',
                content: '[Audio]'
              }]);
            }
            setWhoIsSpeaking('user');
            break;
          }
        case 'conversation.item.input_audio_transcription.completed':
          {
            const itemId = msg.item_id;
            const transcript = (msg.transcript || '[Audio]').trim();
            setMessages(prev => prev.map(m => m.id === itemId && m.role === 'user' ? {
              ...m,
              content: transcript
            } : m));
            break;
          }
        case 'response.audio_transcript.done':
          {
            const itemId = msg.item_id;
            const transcript = (msg.transcript || '[Audio]').trim();
            setWhoIsSpeaking('assistant');
            if (!processedItemIdsRef.current.has(itemId)) {
              processedItemIdsRef.current.add(itemId);
              setMessages(prev => [...prev, {
                id: itemId,
                role: 'assistant',
                content: transcript
              }]);
            }
            break;
          }
        case 'response.function_call_arguments.done':
          {
            const {
              call_id,
              name,
              arguments: rawArgs
            } = msg;
            debugLog(DEBUG_LEVELS.low, 'Function call requested.', call_id, name);
            handleFunctionCall(call_id, name, rawArgs);
            break;
          }
        case 'response.done':
          {
            const resp = msg.response;
            if (resp !== null && resp !== void 0 && resp.usage) {
              const usageStats = parseUsage(resp.usage);
              if (usageStats) {
                setStatistics(prev => {
                  const updated = {
                    text_input_tokens: (prev.text_input_tokens || 0) + usageStats.text_input_tokens,
                    audio_input_tokens: (prev.audio_input_tokens || 0) + usageStats.audio_input_tokens,
                    text_output_tokens: (prev.text_output_tokens || 0) + usageStats.text_output_tokens,
                    audio_output_tokens: (prev.audio_output_tokens || 0) + usageStats.audio_output_tokens,
                    text_cached_tokens: (prev.text_cached_tokens || 0) + usageStats.text_cached_tokens,
                    audio_cached_tokens: (prev.audio_cached_tokens || 0) + usageStats.audio_cached_tokens
                  };
                  commitStatsToServer(updated);
                  return updated;
                });
              }
            }
            setWhoIsSpeaking('user');
            break;
          }
        default:
          // ignore partial or unrecognized events
          break;
      }
    });
    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);
    const baseUrl = 'https://api.openai.com/v1/realtime';
    const chosenModel = model || 'gpt-4o-preview-2024-12-17';
    const sdpResponse = await fetch(`${baseUrl}?model=${chosenModel}`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${clientSecret}`,
        'Content-Type': 'application/sdp'
      },
      body: offer.sdp
    });
    if (!sdpResponse.ok) {
      console.error('SDP exchange failed.', sdpResponse);
      setIsConnecting(false);
      return;
    }
    const answerSDP = await sdpResponse.text();
    await pc.setRemoteDescription({
      type: 'answer',
      sdp: answerSDP
    });
    debugLog(DEBUG_LEVELS.low, 'Realtime connection established.');
    setIsConnecting(false);
    setIsSessionActive(true);
    setIsPaused(false);
    setWhoIsSpeaking('user');
  }, [enableAudioTranscription, handleFunctionCall, commitStatsToServer, setError]);

  /**
   * Stop the Realtime connection.
   */
  const stopRealtimeConnection = useCallback(() => {
    // Emit session ending event
    if (eventLogs && eventEmitterRef.current) {
      eventEmitterRef.current.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_4__.STREAM_TYPES.STATUS, 'Ending realtime session...', {
        visibility: 'visible'
      });
    }
    try {
      if (pcRef.current) {
        pcRef.current.close();
        pcRef.current = null;
      }
      if (localStreamRef.current) {
        localStreamRef.current.getTracks().forEach(track => track.stop());
        localStreamRef.current = null;
      }
      dataChannelRef.current = null;
      setIsConnecting(false);
      setIsSessionActive(false);
      setIsPaused(false);
      setWhoIsSpeaking(null);

      // Commit messages
      onCommitDiscussions(messages);

      // Reset states
      setMessages([]);
      setStatistics({
        text_input_tokens: 0,
        audio_input_tokens: 0,
        text_output_tokens: 0,
        audio_output_tokens: 0,
        text_cached_tokens: 0,
        audio_cached_tokens: 0
      });
      debugLog(DEBUG_LEVELS.low, 'Stopped Realtime connection.');
    } catch (err) {
      console.error('Error stopping connection.', err);
    }
  }, [messages, statistics, onCommitDiscussions]);

  // Update the ref when the function changes
  useEffect(() => {
    stopRealtimeConnectionRef.current = stopRealtimeConnection;
  }, [stopRealtimeConnection]);

  /**
   * Toggle microphone (pause/unpause).
   */
  const togglePause = useCallback(() => {
    if (!localStreamRef.current) return;
    const tracks = localStreamRef.current.getAudioTracks();
    if (!tracks.length) return;
    if (isPaused) {
      tracks.forEach(track => {
        track.enabled = true;
      });
      debugLog(DEBUG_LEVELS.low, 'Resumed microphone.');
      setIsPaused(false);
    } else {
      tracks.forEach(track => {
        track.enabled = false;
      });
      debugLog(DEBUG_LEVELS.low, 'Paused microphone.');
      setIsPaused(true);
    }
  }, [isPaused]);

  /**
   * Initiate the Realtime session on the server, then connect.
   */
  const handlePlay = useCallback(async () => {
    setIsConnecting(true);
    try {
      const data = await onStartRealtimeSession();
      if (!(data !== null && data !== void 0 && data.success)) {
        console.error('Could not start realtime session.', data);
        setIsConnecting(false);
        // Show error to user
        const errorMessage = (data === null || data === void 0 ? void 0 : data.message) || 'Could not start realtime session.';
        setError(errorMessage);
        return;
      }
      functionCallbacksRef.current = data.function_callbacks || [];
      setSessionId(data.session_id);
      await startRealtimeConnection(data.client_secret, data.model);
    } catch (err) {
      console.error('Error in handlePlay.', err);
      setIsConnecting(false);
      // Show error to user
      const errorMessage = err.message || 'An error occurred while starting the realtime session.';
      setError(errorMessage);
    }
  }, [onStartRealtimeSession, startRealtimeConnection, setError]);
  const handleStop = useCallback(() => stopRealtimeConnection(), [stopRealtimeConnection]);

  // Toggles
  const toggleUsers = useCallback(() => setShowUsers(p => !p), []);
  const toggleStatistics = useCallback(() => setShowStatistics(p => !p), []);
  const toggleCaptions = useCallback(() => setShowCaptions(p => !p), []);

  // Class for Pause button
  const pauseButtonClass = useMemo(() => isPaused ? 'mwai-pause mwai-active' : 'mwai-pause', [isPaused]);
  const latestAssistantMessage = useMemo(() => {
    const reversed = [...messages].reverse();
    const last = reversed.find(m => m.role === 'assistant');
    if (!last) return '...';
    if (last.content.length > 256) return `${last.content.slice(0, 256)}...`;
    return last.content;
  }, [messages]);
  const usersOptionClasses = useMemo(() => showUsers ? 'mwai-option mwai-option-users mwai-active' : 'mwai-option mwai-option-users', [showUsers]);
  const captionsOptionClasses = useMemo(() => showCaptions ? 'mwai-option mwai-option-captions mwai-active' : 'mwai-option mwai-option-captions', [showCaptions]);
  const statisticsOptionClasses = useMemo(() => showStatistics ? 'mwai-option mwai-option-statistics mwai-active' : 'mwai-option mwai-option-statistics', [showStatistics]);
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("audio", {
    id: "mwai-audio",
    autoPlay: true
  }), showUsers && /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      justifyContent: 'center'
    }
  }, /*#__PURE__*/React.createElement(_AudioVisualizer__WEBPACK_IMPORTED_MODULE_5__["default"], {
    assistantStream: assistantStream,
    userUI: userUI,
    assistantUI: assistantUI,
    userStream: localStreamRef.current
  })), /*#__PURE__*/React.createElement("div", {
    className: "mwai-controls"
  }, !isSessionActive && !isConnecting && /*#__PURE__*/React.createElement("button", {
    onClick: handlePlay,
    className: "mwai-play",
    disabled: busy || locked,
    "aria-label": "Play"
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_6__["default"], {
    size: 16
  })), isConnecting && /*#__PURE__*/React.createElement("button", {
    className: "mwai-play",
    disabled: true
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_7__["default"], {
    size: 16,
    style: {
      animation: 'spin 0.8s linear infinite'
    }
  })), isSessionActive && !isConnecting && /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("button", {
    onClick: handleStop,
    className: "mwai-stop",
    disabled: busy || locked,
    "aria-label": "Stop"
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_8__["default"], {
    size: 16
  })), /*#__PURE__*/React.createElement("button", {
    onClick: togglePause,
    className: pauseButtonClass,
    disabled: busy || locked,
    "aria-label": "Pause"
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_9__["default"], {
    size: 16
  })))), showCaptions && latestAssistantMessage && latestAssistantMessage.length > 0 && /*#__PURE__*/React.createElement("div", {
    className: "mwai-last-transcript"
  }, latestAssistantMessage), showStatistics && /*#__PURE__*/React.createElement("div", {
    className: "mwai-statistics"
  }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("label", null, "Text In"), /*#__PURE__*/React.createElement("span", null, statistics.text_input_tokens)), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("label", null, "Text Out"), /*#__PURE__*/React.createElement("span", null, statistics.text_output_tokens)), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("label", null, "Text Cached"), /*#__PURE__*/React.createElement("span", null, statistics.text_cached_tokens)), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("label", null, "Audio In"), /*#__PURE__*/React.createElement("span", null, statistics.audio_input_tokens)), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("label", null, "Audio Out"), /*#__PURE__*/React.createElement("span", null, statistics.audio_output_tokens)), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("label", null, "Audio Cached"), /*#__PURE__*/React.createElement("span", null, statistics.audio_cached_tokens))), showOptions && /*#__PURE__*/React.createElement("div", {
    className: "mwai-options"
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_10__["default"], {
    size: 13,
    title: "Show Users",
    className: usersOptionClasses,
    onClick: toggleUsers
  }), /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_11__["default"], {
    size: 18,
    title: "Show Captions",
    className: captionsOptionClasses,
    onClick: toggleCaptions
  }), /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_12__["default"], {
    size: 14,
    title: "Show Statistics",
    className: statisticsOptionClasses,
    onClick: toggleStatistics
  })));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotRealtime);

/***/ }),

/***/ "./app/js/chatbot/ChatbotReply.js":
/*!****************************************!*\
  !*** ./app/js/chatbot/ChatbotReply.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @app/chatbot/helpers */ "./app/js/chatbot/helpers.js");
/* harmony import */ var _app_chatbot_ChatbotContext__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/chatbot/ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
/* harmony import */ var _app_chatbot_ChatbotSpinners__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @app/chatbot/ChatbotSpinners */ "./app/js/chatbot/ChatbotSpinners.js");
/* harmony import */ var _app_components_ReplyActions__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @app/components/ReplyActions */ "./app/js/components/ReplyActions.js");
/* harmony import */ var _ChatbotName__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ChatbotName */ "./app/js/chatbot/ChatbotName.js");
/* harmony import */ var _ChatbotContent__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ChatbotContent */ "./app/js/chatbot/ChatbotContent.js");
// React & Vendor Libs
const {
  useState,
  useMemo,
  useEffect,
  useRef
} = wp.element;


// AI Engine








// AI Engine (Used by TypedMessage)
//import Typed from 'typed.js';
//import { useInterval } from '@app/chatbot/helpers';
//import { applyFilters } from '@app/chatbot/MwaiAPI';

// If isUser, we render the content as-is, otherwise we render it as markdown.
const RawMessage = ({
  message,
  onRendered = () => {}
}) => {
  const {
    state
  } = (0,_app_chatbot_ChatbotContext__WEBPACK_IMPORTED_MODULE_0__.useChatbotContext)();
  const {
    copyButton,
    debugMode
  } = state;
  const [isLongProcess] = useState(message.isQuerying || message.isStreaming);
  const isQuerying = message.isQuerying;
  const isStreaming = message.isStreaming;
  useEffect(() => {
    if (!isLongProcess) {
      onRendered();
    } else if (isLongProcess && !isQuerying && !isStreaming) {
      onRendered();
    }
  }, [isLongProcess, isQuerying, isStreaming]);

  // For non-streaming queries, show bouncing dots
  if (isQuerying) {
    return /*#__PURE__*/React.createElement(_app_chatbot_ChatbotSpinners__WEBPACK_IMPORTED_MODULE_1__.BouncingDots, null);
  }
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(_ChatbotName__WEBPACK_IMPORTED_MODULE_2__["default"], {
    role: message.role
  }), /*#__PURE__*/React.createElement(_app_components_ReplyActions__WEBPACK_IMPORTED_MODULE_3__["default"], {
    content: message.content,
    enabled: copyButton,
    className: "mwai-text"
  }, /*#__PURE__*/React.createElement(_ChatbotContent__WEBPACK_IMPORTED_MODULE_4__["default"], {
    message: message
  })));
};
const ImagesMessage = ({
  message,
  onRendered = () => {}
}) => {
  const [images, setImages] = useState(message === null || message === void 0 ? void 0 : message.images);
  useEffect(() => {
    onRendered();
  });
  const handleImageError = index => {
    const placeholderImage = "https://placehold.co/600x200?text=Expired+Image";
    setImages(prevImages => prevImages.map((img, i) => i === index ? placeholderImage : img));
  };
  if (message.isQuerying) {
    return /*#__PURE__*/React.createElement(_app_chatbot_ChatbotSpinners__WEBPACK_IMPORTED_MODULE_1__.BouncingDots, null);
  }
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(_ChatbotName__WEBPACK_IMPORTED_MODULE_2__["default"], {
    role: message.role
  }), /*#__PURE__*/React.createElement("span", {
    className: "mwai-text"
  }, /*#__PURE__*/React.createElement("div", {
    className: "mwai-gallery"
  }, images === null || images === void 0 ? void 0 : images.map((image, index) => /*#__PURE__*/React.createElement("a", {
    key: index,
    href: image,
    target: "_blank",
    rel: "noopener noreferrer"
  }, /*#__PURE__*/React.createElement("img", {
    key: index,
    src: image,
    onError: () => handleImageError(index)
  }))))));
};

// const TypedMessage = ({ message, conversationRef, onRendered = () => {} }) => {
//   const typedElement = useRef(null);
//   const [ dynamic ] = useState(message.isQuerying);
//   const [ ready, setReady ] = useState(!message.isQuerying);
//   const content = message.content;

//   useEffect(() => {
//     console.warn("Do not use the Typewriter Effect. Use Streaming instead.");
//   }, []);

//   useInterval(200, () => {
//     if (!conversationRef?.current) {
//       return;
//     }
//   }, !ready);

//   useEffect(() => {
//     if (!dynamic) {
//       onRendered();
//       return;
//     }

//     if (!typedElement.current) {
//       return;
//     }

//     const options = {
//       strings: [content],
//       typeSpeed: applyFilters('typewriter.speed', 15),
//       showCursor: false,
//       onComplete: (self) => {
//         if (self.cursor) {
//           self.cursor.remove();
//         }
//         onRendered();
//         setReady(() => true);
//       },
//     };

//     const typed = new Typed(typedElement.current, options);
//     return () => { typed.destroy(); };
//   }, [message, message.isQuerying]);

//   const renderedContent = useMemo(() => {
//     let out = "";
//     try {
//       out = compiler(content);
//     }
//     catch (e) {
//       console.error("Crash in markdown-to-jsx! Reverting to plain text.", { e, content });
//       out = content;
//     }
//     return out;
//   }, [content]);

//   return (
//     <>
//       {message.isQuerying && <BouncingDots />}
//       {!message.isQuerying && dynamic && <>
//         <ChatbotName role={message.role} />
//         <span className="mwai-text" ref={typedElement} />
//       </>}
//       {!message.isQuerying && !dynamic && <>
//         <ChatbotName role={message.role} />
//         <span className="mwai-text">
//           {renderedContent}
//         </span>
//       </>}
//     </>
//   );
// };

const ChatbotReply = ({
  message,
  conversationRef
}) => {
  var _message$images;
  const {
    state
  } = (0,_app_chatbot_ChatbotContext__WEBPACK_IMPORTED_MODULE_0__.useChatbotContext)();
  const {
    typewriter
  } = state;
  const css = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_5__.useClasses)();
  const mainElement = useRef();
  const classes = css('mwai-reply', {
    'mwai-ai': message.role === 'assistant',
    'mwai-user': message.role === 'user',
    'mwai-system': message.role === 'system'
  });
  const isImages = (message === null || message === void 0 || (_message$images = message.images) === null || _message$images === void 0 ? void 0 : _message$images.length) > 0;
  const onRendered = () => {
    if (!mainElement.current) {
      return;
    }
    if (message.isQuerying) {
      return;
    }
    if (mainElement.current.classList.contains('mwai-rendered')) {
      return;
    }
    if (typeof hljs !== 'undefined') {
      mainElement.current.classList.add('mwai-rendered');
      const selector = mainElement.current.querySelectorAll('pre code');
      selector.forEach(el => {
        // eslint-disable-next-line no-undef
        hljs.highlightElement(el);
      });
    }
  };
  const output = useMemo(() => {
    if (message.role === 'user') {
      return /*#__PURE__*/React.createElement("div", {
        ref: mainElement,
        className: classes
      }, /*#__PURE__*/React.createElement(RawMessage, {
        message: message
      }));
    }
    if (message.role === 'assistant') {
      if (isImages) {
        return /*#__PURE__*/React.createElement("div", {
          ref: mainElement,
          className: classes
        }, /*#__PURE__*/React.createElement(ImagesMessage, {
          message: message,
          conversationRef: conversationRef,
          onRendered: onRendered
        }));
      }
      // else if (typewriter && !message.isStreaming) {
      //   console.warn("The Typewriter effect is deprecated. Use Streaming instead.");
      //   return <div ref={mainElement} className={classes}>
      //     <TypedMessage message={message} conversationRef={conversationRef} onRendered={onRendered} />
      //   </div>;
      // }
      return /*#__PURE__*/React.createElement("div", {
        ref: mainElement,
        className: classes
      }, /*#__PURE__*/React.createElement(RawMessage, {
        message: message,
        conversationRef: conversationRef,
        onRendered: onRendered
      }));
    }
    if (message.role === 'system') {
      return /*#__PURE__*/React.createElement("div", {
        ref: mainElement,
        className: classes
      }, /*#__PURE__*/React.createElement(RawMessage, {
        message: message,
        conversationRef: conversationRef,
        onRendered: onRendered
      }));
    }
    return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("i", null, "Unhandled role."));
  }, [message, conversationRef, isImages, typewriter]);
  return output;
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotReply);

/***/ }),

/***/ "./app/js/chatbot/ChatbotSpinners.js":
/*!*******************************************!*\
  !*** ./app/js/chatbot/ChatbotSpinners.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BouncingDots: () => (/* binding */ BouncingDots)
/* harmony export */ });
const BouncingDots = () => {
  const bouncingLoaderStyles = {
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    width: '100%',
    height: 26
  };
  const bouncingDotStyles = {
    width: 9,
    height: 9,
    margin: '5px 0px 0px 5px',
    borderRadius: '50%',
    backgroundColor: '#a3a1a1',
    opacity: 1,
    animation: 'bouncing-loader 0.4s infinite alternate'
  };
  const animationDelays = ['0.1s', '0.2s', '0.3s'];
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("style", null, `
          @keyframes bouncing-loader {
            to {
              opacity: 0.6;
              transform: translateY(-10px);
            }
          }
        `), /*#__PURE__*/React.createElement("div", {
    style: bouncingLoaderStyles
  }, animationDelays.map((delay, index) => /*#__PURE__*/React.createElement("div", {
    key: index,
    style: {
      ...bouncingDotStyles,
      animationDelay: delay
    }
  }))));
};


/***/ }),

/***/ "./app/js/chatbot/ChatbotSubmit.js":
/*!*****************************************!*\
  !*** ./app/js/chatbot/ChatbotSubmit.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/send.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/eraser.js");
/* harmony import */ var _ChatbotContext__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
// React & Vendor Libs
const {
  useMemo,
  useCallback
} = wp.element;


const ChatbotSubmit = () => {
  const {
    state,
    actions
  } = (0,_ChatbotContext__WEBPACK_IMPORTED_MODULE_0__.useChatbotContext)();
  const {
    onClear,
    onSubmitAction,
    setIsListening
  } = actions;
  const {
    textClear,
    textSend,
    uploadedFile,
    inputText,
    messages,
    isListening,
    timeElapsed,
    busy,
    submitButtonConf,
    locked
  } = state;
  const isFileUploading = !!(uploadedFile !== null && uploadedFile !== void 0 && uploadedFile.uploadProgress);
  const hasFileUploaded = !!(uploadedFile !== null && uploadedFile !== void 0 && uploadedFile.uploadedId);
  const clearMode = !hasFileUploaded && inputText.length < 1 && (messages === null || messages === void 0 ? void 0 : messages.length) > 1;
  const buttonContent = useMemo(() => {
    if (busy) {
      return timeElapsed ? /*#__PURE__*/React.createElement("div", {
        className: "mwai-timer"
      }, timeElapsed) : null;
    }
    // If there are text values for the button, use them
    if (submitButtonConf !== null && submitButtonConf !== void 0 && submitButtonConf.imageSend && submitButtonConf !== null && submitButtonConf !== void 0 && submitButtonConf.imageClear) {
      return /*#__PURE__*/React.createElement("img", {
        src: clearMode ? submitButtonConf.imageClear : submitButtonConf.imageSend,
        alt: clearMode ? textClear : textSend
      });
    }
    // If there are no text or images, use the default send icon
    if (!clearMode && !textSend) {
      return /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_1__["default"], {
        size: "20",
        style: {
          marginLeft: 10
        }
      });
    }
    if (clearMode && !textClear) {
      return /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_2__["default"], {
        size: "20"
      });
    }
    return /*#__PURE__*/React.createElement("span", null, clearMode ? textClear : textSend);
  }, [busy, timeElapsed, clearMode, textClear, textSend, submitButtonConf]);
  const buttonClassName = useMemo(() => {
    return `mwai-input-submit ${busy ? 'mwai-busy' : ''}`;
  }, [busy]);
  const onSubmitClick = useCallback(() => {
    if (isListening) {
      setIsListening(false);
    }
    if (clearMode) {
      onClear();
    } else {
      onSubmitAction();
    }
  }, [clearMode, isListening, onClear, onSubmitAction, setIsListening]);
  const handleClick = useCallback(() => {
    if (!busy) {
      onSubmitClick();
    }
  }, [busy, onSubmitClick]);
  return /*#__PURE__*/React.createElement("button", {
    className: buttonClassName,
    disabled: busy || isFileUploading || locked,
    onClick: handleClick
  }, buttonContent);
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotSubmit);

/***/ }),

/***/ "./app/js/chatbot/ChatbotSystem.js":
/*!*****************************************!*\
  !*** ./app/js/chatbot/ChatbotSystem.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _app_chatbot_ChatbotContext__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/chatbot/ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
/* harmony import */ var _app_chatbot_ChatbotUI__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @app/chatbot/ChatbotUI */ "./app/js/chatbot/ChatbotUI.js");


const ChatbotSystem = props => {
  return /*#__PURE__*/React.createElement(_app_chatbot_ChatbotContext__WEBPACK_IMPORTED_MODULE_0__.ChatbotContextProvider, props, /*#__PURE__*/React.createElement(_app_chatbot_ChatbotUI__WEBPACK_IMPORTED_MODULE_1__["default"], props));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotSystem);

/***/ }),

/***/ "./app/js/chatbot/ChatbotTrigger.js":
/*!******************************************!*\
  !*** ./app/js/chatbot/ChatbotTrigger.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _ChatbotContext__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./helpers */ "./app/js/chatbot/helpers.js");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../helpers */ "./app/js/helpers.js");
// React & Vendor Libs
const {
  useMemo,
  useEffect
} = wp.element;



const ChatbotTrigger = () => {
  const {
    state,
    actions
  } = (0,_ChatbotContext__WEBPACK_IMPORTED_MODULE_0__.useChatbotContext)();
  const {
    isWindow,
    iconText,
    showIconMessage,
    iconAlt,
    iconUrl,
    open
  } = state;
  const {
    setShowIconMessage,
    setOpen
  } = actions;
  useEffect(() => {
    if (open && showIconMessage) {
      setShowIconMessage(false);
    }
  }, [open, setShowIconMessage, showIconMessage]);
  const triggerContent = useMemo(() => {
    if (!isWindow) {
      return null;
    }
    const renderIcon = () => {
      if ((0,_helpers__WEBPACK_IMPORTED_MODULE_1__.isEmoji)(iconUrl)) {
        return /*#__PURE__*/React.createElement("div", {
          className: "mwai-icon mwai-emoji",
          style: {
            fontSize: '48px',
            lineHeight: '64px',
            width: '64px',
            height: '64px',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center'
          }
        }, iconUrl);
      } else {
        return /*#__PURE__*/React.createElement("img", {
          className: "mwai-icon",
          width: "64",
          height: "64",
          alt: iconAlt,
          src: iconUrl
        });
      }
    };

    // TODO: Let's remove mwai-open-button at some point.
    return /*#__PURE__*/React.createElement("div", {
      className: "mwai-trigger mwai-open-button"
    }, /*#__PURE__*/React.createElement(_helpers__WEBPACK_IMPORTED_MODULE_2__.TransitionBlock, {
      className: "mwai-icon-text-container",
      if: iconText && showIconMessage
    }, /*#__PURE__*/React.createElement("div", {
      className: "mwai-icon-text-close",
      onClick: () => setShowIconMessage(false)
    }, "\u2715"), /*#__PURE__*/React.createElement("div", {
      className: "mwai-icon-text",
      onClick: () => setOpen(true)
    }, iconText)), /*#__PURE__*/React.createElement("div", {
      className: "mwai-icon-container",
      onClick: () => setOpen(true)
    }, renderIcon()));
  }, [isWindow, iconText, showIconMessage, iconAlt, iconUrl, setShowIconMessage, setOpen]);
  return /*#__PURE__*/React.createElement(React.Fragment, null, triggerContent);
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotTrigger);

/***/ }),

/***/ "./app/js/chatbot/ChatbotUI.js":
/*!*************************************!*\
  !*** ./app/js/chatbot/ChatbotUI.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/chatbot/helpers */ "./app/js/chatbot/helpers.js");
/* harmony import */ var _app_chatbot_ChatbotContext__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @app/chatbot/ChatbotContext */ "./app/js/chatbot/ChatbotContext.js");
/* harmony import */ var _ChatbotReply__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ChatbotReply */ "./app/js/chatbot/ChatbotReply.js");
/* harmony import */ var _ChatbotHeader__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ChatbotHeader */ "./app/js/chatbot/ChatbotHeader.js");
/* harmony import */ var _ChatbotTrigger__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ChatbotTrigger */ "./app/js/chatbot/ChatbotTrigger.js");
/* harmony import */ var _ChatbotBody__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./ChatbotBody */ "./app/js/chatbot/ChatbotBody.js");
// React & Vendor Libs
const {
  useState,
  useMemo,
  useLayoutEffect,
  useCallback,
  useEffect,
  useRef
} = wp.element;






const isImage = file => file.type.startsWith('image/');
const isDocument = file => {
  const allowedDocumentTypes = ['text/x-c', 'text/x-csharp', 'text/x-c++', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/html', 'text/x-java', 'application/json', 'text/markdown', 'application/pdf', 'text/x-php', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'text/x-python', 'text/x-script.python', 'text/x-ruby', 'text/x-tex', 'text/plain', 'text/css', 'text/javascript', 'application/x-sh', 'application/typescript'];
  return allowedDocumentTypes.includes(file.type);
};
const ChatbotUI = props => {
  const css = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.useClasses)();
  const {
    style
  } = props;
  const [autoScroll, setAutoScroll] = useState(true);
  const {
    state,
    actions
  } = (0,_app_chatbot_ChatbotContext__WEBPACK_IMPORTED_MODULE_1__.useChatbotContext)();
  const {
    theme,
    botId,
    customId,
    messages,
    textCompliance,
    isWindow,
    fullscreen,
    iconPosition,
    iconBubble,
    shortcuts,
    blocks,
    imageUpload,
    fileSearch,
    fileUpload,
    draggingType,
    isBlocked,
    virtualKeyboardFix,
    windowed,
    cssVariables,
    conversationRef,
    open,
    busy,
    uploadIconPosition
  } = state;
  const {
    onSubmit,
    setIsBlocked,
    setDraggingType,
    onUploadFile
  } = actions;
  const themeStyle = useMemo(() => (theme === null || theme === void 0 ? void 0 : theme.type) === 'css' ? theme === null || theme === void 0 ? void 0 : theme.style : null, [theme]);
  const needTools = imageUpload || fileSearch || fileUpload;
  const needsFooter = needTools || textCompliance;
  const timeoutRef = useRef(null);

  // #region Attempt to fix Android & iOS Virtual Keyboard
  const {
    viewportHeight,
    isIOS,
    isAndroid
  } = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.useViewport)();
  useEffect(() => {
    if (!virtualKeyboardFix) {
      return;
    }
    if (!(isIOS || isAndroid)) {
      return;
    }
    if (!isWindow) {
      return;
    }
    const scrollableDiv = document.querySelector('.mwai-window');
    if (scrollableDiv) {
      if (open) {
        scrollableDiv.style.height = `${viewportHeight}px`;
        if (isIOS) {
          const scrollToTop = () => {
            if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') {
              window.scrollTo({
                top: 0
              });
              // Unfortunately, the first scrollTo doesn't always work, so we need to do it again.
              const scrollInterval = setInterval(() => {
                window.scrollTo({
                  top: 0
                });
              }, 100);
              setTimeout(() => {
                clearInterval(scrollInterval);
              }, 1000);
            }
          };
          scrollToTop();
        }
      } else {
        //console.log("UNSET THE HEIGHT");
        scrollableDiv.style.height = '';
      }
    }
  }, [fullscreen, isAndroid, isIOS, isWindow, windowed, open, viewportHeight, virtualKeyboardFix]);
  // #endregion

  const baseClasses = css('mwai-chatbot', {
    [`mwai-${theme === null || theme === void 0 ? void 0 : theme.themeId}-theme`]: true,
    'mwai-window': isWindow,
    'mwai-bubble': iconBubble,
    'mwai-open': open,
    'mwai-fullscreen': !windowed || !isWindow && fullscreen,
    'mwai-bottom-left': iconPosition === 'bottom-left',
    'mwai-top-right': iconPosition === 'top-right',
    'mwai-top-left': iconPosition === 'top-left'
  });

  // #region Auto Scroll
  useLayoutEffect(() => {
    if (autoScroll && conversationRef.current) {
      conversationRef.current.scrollTop = conversationRef.current.scrollHeight;
    }
  }, [messages, autoScroll, conversationRef, busy]);
  const onScroll = () => {
    if (conversationRef.current) {
      const {
        scrollTop,
        scrollHeight,
        clientHeight
      } = conversationRef.current;
      const isAtBottom = scrollHeight - scrollTop <= clientHeight + 1; // Allowing a small margin
      setAutoScroll(isAtBottom);
    }
  };
  // #endregion
  // eslint-disable-next-line no-undef
  const executedScripts = useRef(new Set());
  const simpleHash = str => {
    let hash = 0,
      i,
      chr;
    if (str.length === 0) return hash;
    for (i = 0; i < str.length; i++) {
      chr = str.charCodeAt(i);
      hash = (hash << 5) - hash + chr;
      hash |= 0; // Convert to 32bit integer
    }
    return hash;
  };

  // Safer executeScript
  // const executeScript = (scriptContent) => {
  //   const scriptHash = simpleHash(scriptContent);
  //   if (executedScripts.current.has(scriptHash)) {
  //     return;
  //   }
  //   // Wrap in an IIFE + try/catch
  //   const wrappedCode = `
  //     (function() {
  //       try {
  //         ${scriptContent}
  //       } catch (err) {
  //         console.error("User script error:", err);
  //       }
  //     })();
  //   `;
  //   const script = document.createElement('script');
  //   script.type = 'text/javascript';
  //   script.textContent = wrappedCode;
  //   document.body.appendChild(script);
  //   executedScripts.current.add(scriptHash);
  // };

  // Original executeScript
  const executeScript = scriptContent => {
    const scriptHash = simpleHash(scriptContent);
    if (!executedScripts.current.has(scriptHash)) {
      const script = document.createElement('script');
      script.type = 'text/javascript';
      script.textContent = scriptContent;
      document.body.appendChild(script);
      executedScripts.current.add(scriptHash);
    }
  };
  useEffect(() => {
    if (blocks && blocks.length > 0) {
      blocks.forEach(block => {
        const {
          type,
          data
        } = block;
        if (type === 'content' && data.script) {
          executeScript(data.script);
        }
      });
    }
  }, [blocks]);
  const messageList = useMemo(() => messages === null || messages === void 0 ? void 0 : messages.map(message => /*#__PURE__*/React.createElement(_ChatbotReply__WEBPACK_IMPORTED_MODULE_2__["default"], {
    key: message.id,
    message: message
  })), [messages]);
  const jsxShortcuts = useMemo(() => {
    if (!shortcuts || shortcuts.length === 0) {
      return null;
    }
    return /*#__PURE__*/React.createElement("div", {
      className: "mwai-shortcuts"
    }, shortcuts.map((action, index) => {
      const {
        type,
        data
      } = action;

      // Common extraction (label, variant, icon, etc.)
      // This part can be repeated or factored out depending on your preference.
      const {
        label,
        variant,
        icon,
        className
      } = data ?? {};

      // Base button classes
      let baseClasses = css('mwai-shortcut', {
        'mwai-success': variant === 'success',
        'mwai-danger': variant === 'danger',
        'mwai-warning': variant === 'warning',
        'mwai-info': variant === 'info'
      });
      if (className) {
        baseClasses += ` ${className}`;
      }

      // Check icon type
      const iconIsURL = icon && icon.startsWith('http');
      const iconIsEmoji = icon && !iconIsURL && icon.length >= 1 && icon.length <= 2;

      // Use a switch for clarity
      switch (type) {
        case 'message':
          {
            // For 'message' type, call onSubmit
            const {
              message
            } = data;
            const onClick = () => {
              onSubmit(message);
            };
            return /*#__PURE__*/React.createElement("button", {
              className: baseClasses,
              key: index,
              onClick: onClick
            }, (iconIsURL || iconIsEmoji) && /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
              className: "mwai-icon"
            }, iconIsURL && /*#__PURE__*/React.createElement("img", {
              src: icon,
              alt: label || "AI Shortcut"
            }), iconIsEmoji && /*#__PURE__*/React.createElement("span", {
              role: "img",
              "aria-label": "AI Shortcut"
            }, icon)), /*#__PURE__*/React.createElement("div", {
              style: {
                flex: 'auto'
              }
            })), /*#__PURE__*/React.createElement("div", {
              className: "mwai-label"
            }, label || "N/A"));
          }
        case 'callback':
          {
            // For 'callback' type, call the function in data.onClick
            const {
              onClick: customOnClick
            } = data;
            const onClickHandler = () => {
              if (typeof customOnClick === 'function') {
                customOnClick();
              } else if (typeof customOnClick === 'string') {
                const replacedOnClick = customOnClick.replace(/{CHATBOT_ID}/g, botId);
                const parsedFunction = new Function(`return (${replacedOnClick});`)();
                data.onClick = parsedFunction;
                parsedFunction();
              } else {
                console.warn('No valid callback function provided in data.onClick.');
              }
            };
            return /*#__PURE__*/React.createElement("button", {
              className: baseClasses,
              key: index,
              onClick: onClickHandler
            }, (iconIsURL || iconIsEmoji) && /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
              className: "mwai-icon"
            }, iconIsURL && /*#__PURE__*/React.createElement("img", {
              src: icon,
              alt: label || "AI Shortcut"
            }), iconIsEmoji && /*#__PURE__*/React.createElement("span", {
              role: "img",
              "aria-label": "AI Shortcut"
            }, icon)), /*#__PURE__*/React.createElement("div", {
              style: {
                flex: 'auto'
              }
            })), /*#__PURE__*/React.createElement("div", {
              className: "mwai-label"
            }, label || "N/A"));
          }
        default:
          {
            console.warn(`This shortcut type is not supported: ${type}.`);
            return null;
          }
      }
    }));
  }, [css, onSubmit, shortcuts]);
  const jsxBlocks = useMemo(() => {
    if (!blocks || blocks.length === 0) {
      return null;
    }
    return /*#__PURE__*/React.createElement("div", {
      className: "mwai-blocks"
    }, blocks.map((block, index) => {
      const {
        type,
        data
      } = block;
      if (type !== 'content') {
        console.warn(`Block type ${type} is not supported.`);
        return null;
      }
      const {
        html,
        variant
      } = data;
      const baseClasses = css('mwai-block', {
        'mwai-success': variant === 'success',
        'mwai-danger': variant === 'danger',
        'mwai-warning': variant === 'warning',
        'mwai-info': variant === 'info'
      });
      return /*#__PURE__*/React.createElement("div", {
        className: baseClasses,
        key: index,
        dangerouslySetInnerHTML: {
          __html: html
        }
      });
    }));
  }, [css, blocks]);
  const handleDrag = useCallback((event, dragState) => {
    event.preventDefault();
    event.stopPropagation();
    const file = event.dataTransfer.items[0];
    if (dragState) {
      // Clear any existing timeout
      if (timeoutRef.current) {
        clearTimeout(timeoutRef.current);
        timeoutRef.current = null;
      }
      if (imageUpload && isImage(file)) {
        setDraggingType('image');
        setIsBlocked(false);
      } else if ((fileSearch || fileUpload) && isDocument(file)) {
        setDraggingType('document');
        setIsBlocked(false);
      } else {
        setDraggingType(false);
        setIsBlocked(true);
      }
    } else {
      // Set a timeout before changing the state
      if (!timeoutRef.current) {
        timeoutRef.current = setTimeout(() => {
          setDraggingType(false);
          setIsBlocked(false);
          timeoutRef.current = null;
        }, 100); // Adjust this delay as needed
      }
    }
  }, [imageUpload, fileSearch, fileUpload]);
  const handleDrop = useCallback(event => {
    event.preventDefault();
    handleDrag(event, false);
    if (busy) return;
    const file = event.dataTransfer.files[0];
    if (file) {
      if (draggingType === 'image' && imageUpload) {
        onUploadFile(file);
      } else if (draggingType === 'document' && (fileSearch || fileUpload)) {
        onUploadFile(file);
      } else {
        // Indicate that the drop is not valid
        setIsBlocked(true);
        setTimeout(() => setIsBlocked(false), 2000); // Reset after 2 seconds
      }
    }
  }, [busy, draggingType, imageUpload, fileUpload, fileSearch, onUploadFile]);
  const inputClassNames = css('mwai-input', {
    'mwai-dragging': draggingType,
    'mwai-blocked': isBlocked
  });
  return /*#__PURE__*/React.createElement(_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.TransitionBlock, {
    dir: "auto",
    id: `mwai-chatbot-${customId || botId}`,
    className: baseClasses,
    style: {
      ...cssVariables,
      ...style
    },
    if: true,
    disableTransition: !isWindow
  }, themeStyle && /*#__PURE__*/React.createElement("style", null, themeStyle), /*#__PURE__*/React.createElement(_ChatbotTrigger__WEBPACK_IMPORTED_MODULE_3__["default"], null), /*#__PURE__*/React.createElement(_ChatbotHeader__WEBPACK_IMPORTED_MODULE_4__["default"], null), /*#__PURE__*/React.createElement(_ChatbotBody__WEBPACK_IMPORTED_MODULE_5__["default"], {
    conversationRef: conversationRef,
    onScroll: onScroll,
    messageList: messageList,
    jsxShortcuts: jsxShortcuts,
    jsxBlocks: jsxBlocks,
    inputClassNames: inputClassNames,
    handleDrop: handleDrop,
    handleDrag: handleDrag,
    needsFooter: needsFooter,
    needTools: needTools,
    uploadIconPosition: uploadIconPosition
  }));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ChatbotUI);

/***/ }),

/***/ "./app/js/chatbot/DiscussionsContext.js":
/*!**********************************************!*\
  !*** ./app/js/chatbot/DiscussionsContext.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DiscussionsContextProvider: () => (/* binding */ DiscussionsContextProvider),
/* harmony export */   useDiscussionsContext: () => (/* binding */ useDiscussionsContext)
/* harmony export */ });
/* harmony import */ var _app_helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @app/helpers */ "./app/js/helpers.js");
/* harmony import */ var _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/helpers/tokenManager */ "./app/js/helpers/tokenManager.js");
// React & Vendor Libs
const {
  useContext,
  createContext,
  useState,
  useMemo,
  useEffect,
  useCallback,
  useRef
} = wp.element;


const DiscussionsContext = createContext();
const useDiscussionsContext = () => {
  const context = useContext(DiscussionsContext);
  if (!context) {
    throw new Error('useDiscussionsContext must be used within a DiscussionsContextProvider');
  }
  return context;
};
const DiscussionsContextProvider = ({
  children,
  ...rest
}) => {
  const {
    system,
    theme
  } = rest;
  const [discussions, setDiscussions] = useState([]);
  const [discussion, setDiscussion] = useState(null);
  const [currentChatId, setCurrentChatId] = useState(null);
  const [busy, setBusy] = useState(false);
  const [currentPage, setCurrentPage] = useState(0);
  const [totalCount, setTotalCount] = useState(0);
  const [paginationBusy, setPaginationBusy] = useState(false);
  const isRefreshing = useRef(false);
  const shortcodeStyles = useMemo(() => (theme === null || theme === void 0 ? void 0 : theme.settings) || {}, [theme]);

  // System Parameters
  const botId = system.botId;
  const customId = system.customId;
  const [restNonce, setRestNonce] = useState(system.restNonce || _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_0__["default"].getToken());
  const restNonceRef = useRef(system.restNonce || _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_0__["default"].getToken());

  // Subscribe to global token updates
  useEffect(() => {
    const unsubscribe = _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_0__["default"].subscribe(newToken => {
      setRestNonce(newToken);
      restNonceRef.current = newToken;
    });
    return unsubscribe;
  }, []);
  const pluginUrl = system.pluginUrl;
  const restUrl = system.restUrl;
  const debugMode = system.debugMode;

  // UI Parameters
  const cssVariables = useMemo(() => {
    const cssVariables = Object.keys(shortcodeStyles).reduce((acc, key) => {
      acc[`--mwai-${key}`] = shortcodeStyles[key];
      return acc;
    }, {});
    return cssVariables;
  }, [shortcodeStyles]);
  const hasEmptyDiscussion = useMemo(() => {
    return discussions.some(discussion => discussion.messages.length === 0);
  }, [discussions]);

  // Get the chatbot's stored chatId from localStorage on initial load
  const getStoredChatId = useCallback(() => {
    const chatbot = MwaiAPI.getChatbot(botId);
    const localStorageKey = chatbot === null || chatbot === void 0 ? void 0 : chatbot.localStorageKey;
    if (localStorageKey) {
      try {
        const storedData = localStorage.getItem(localStorageKey);
        if (storedData) {
          const parsedData = JSON.parse(storedData);
          return parsedData.chatId;
        } else {}
      } catch (e) {
        console.error('[DISCUSSIONS] Error reading chatbot storage:', e);
      }
    }
    return null;
  }, [botId]);
  const refresh = useCallback(async (silentRefresh = false, page = currentPage, isPagination = false) => {
    // Prevent concurrent requests
    if (isRefreshing.current) {
      return;
    }
    isRefreshing.current = true;
    let startTime;
    try {
      if (!silentRefresh) {
        startTime = Date.now();
        if (isPagination) {
          setPaginationBusy(true);
        } else {
          setBusy(true);
        }
      }
      const paging = (system === null || system === void 0 ? void 0 : system.paging) || 0;
      const limit = paging > 0 ? paging : undefined;
      const offset = paging > 0 ? page * paging : 0;
      const body = {
        botId: botId || customId,
        ...(limit && {
          limit,
          offset
        })
      };
      if (debugMode) {}
      const handleTokenUpdate = newToken => {
        setRestNonce(newToken);
        restNonceRef.current = newToken;
        _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_0__["default"].setToken(newToken); // Update globally
      };
      const response = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_1__.mwaiFetch)(`${restUrl}/mwai-ui/v1/discussions/list`, body, restNonceRef.current, false, undefined, handleTokenUpdate);
      const data = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_1__.mwaiHandleRes)(response, null, debugMode ? "DISCUSSIONS" : null, handleTokenUpdate, debugMode);
      if (!data.success) {
        throw new Error(`Could not retrieve the discussions: ${data.message}`);
      }
      if (debugMode) {}
      const conversations = data.chats.map(conversation => {
        const messages = JSON.parse(conversation.messages);
        const extra = JSON.parse(conversation.extra);
        return {
          ...conversation,
          messages,
          extra,
          metadata_display: conversation.metadata_display
        };
      });

      // Update total count for pagination
      if (data.total !== undefined) {
        setTotalCount(data.total);
      }

      // Handle pagination: replace or merge based on page and paging setting
      setDiscussions(prevDiscussions => {
        const paging = (system === null || system === void 0 ? void 0 : system.paging) || 0;
        if (paging > 0) {
          // With pagination enabled, always replace discussions
          return conversations;
        } else {
          // No pagination, merge with local discussions for real-time updates
          const discussionMap = new Map();

          // Add local discussions to the map
          prevDiscussions.forEach(disc => {
            discussionMap.set(disc.chatId, disc);
          });

          // Update or add server discussions
          conversations.forEach(serverDisc => {
            discussionMap.set(serverDisc.chatId, serverDisc);
          });
          const newDiscussions = Array.from(discussionMap.values());

          // Update the selected discussion if necessary
          if (discussion) {
            const updatedDiscussion = newDiscussions.find(disc => disc.chatId === discussion.chatId);
            if (updatedDiscussion && updatedDiscussion !== discussion) {
              setDiscussion(updatedDiscussion);
            }
          }
          return newDiscussions;
        }
      });
    } catch (err) {
      console.error(err);
    } finally {
      isRefreshing.current = false;
      if (!silentRefresh && startTime) {
        // Ensure minimum 500ms display time
        const elapsedTime = Date.now() - startTime;
        const remainingTime = Math.max(0, 200 - elapsedTime);
        setTimeout(() => {
          if (isPagination) {
            setPaginationBusy(false);
          } else {
            setBusy(false);
          }
        }, remainingTime);
      }
    }
  }, [discussion, currentPage, system === null || system === void 0 ? void 0 : system.paging]); // Include dependencies

  const refreshInterval = (system === null || system === void 0 ? void 0 : system.refreshInterval) || 5000;
  useEffect(() => {
    // On initial load, check if chatbot has a stored chatId
    const storedChatId = getStoredChatId();
    if (storedChatId && !currentChatId) {
      setCurrentChatId(storedChatId);
    }
    refresh();
    // Only set up interval if refresh is enabled
    if (refreshInterval > 0) {
      const interval = setInterval(() => {
        refresh(true);
      }, refreshInterval);
      return () => clearInterval(interval);
    }
  }, [refreshInterval, currentPage]);
  useEffect(() => {
    // If we have a currentChatId but no discussion selected, find and select it
    if (currentChatId && !discussion) {
      const foundDiscussion = discussions.find(disc => disc.chatId === currentChatId);
      if (foundDiscussion) {
        setDiscussion(foundDiscussion);
        // Also sync with chatbot when auto-selecting a discussion
        try {
          var _foundDiscussion$extr;
          const chatbot = getChatbot(botId);
          const previousResponseId = ((_foundDiscussion$extr = foundDiscussion.extra) === null || _foundDiscussion$extr === void 0 ? void 0 : _foundDiscussion$extr.previousResponseId) || null;
          chatbot.setContext({
            chatId: foundDiscussion.chatId,
            messages: foundDiscussion.messages,
            previousResponseId
          });
        } catch (error) {
          // Chatbot might not be ready yet, that's ok
          console.debug('Chatbot not ready for auto-selected discussion', error);
        }
      }
    }
    // Update the discussion if it exists in the list
    else if (discussion) {
      const updatedDiscussion = discussions.find(disc => disc.chatId === discussion.chatId);
      if (updatedDiscussion && updatedDiscussion !== discussion) {
        setDiscussion(updatedDiscussion);
      }
    }
  }, [discussions, currentChatId, botId]);
  const getChatbot = botId => {
    const chatbot = MwaiAPI.getChatbot(botId);
    if (!chatbot) {
      throw new Error(`Chatbot not found.`, {
        botId,
        chatbots: MwaiAPI.chatbots
      });
    }
    return chatbot;
  };
  const onDiscussionClick = async chatId => {
    var _selectedDiscussion$e;
    const selectedDiscussion = discussions.find(x => x.chatId === chatId);
    if (!selectedDiscussion) {
      console.error(`Discussion not found.`, {
        chatId,
        discussions
      });
      return;
    }
    const chatbot = getChatbot(botId);

    // Extract previousResponseId from discussion extra data
    const previousResponseId = ((_selectedDiscussion$e = selectedDiscussion.extra) === null || _selectedDiscussion$e === void 0 ? void 0 : _selectedDiscussion$e.previousResponseId) || null;
    chatbot.setConversation({
      chatId,
      messages: selectedDiscussion.messages,
      previousResponseId
    });
    setDiscussion(selectedDiscussion);
    setCurrentChatId(chatId);
  };
  const onEditDiscussion = async discussionToEdit => {
    const newTitle = prompt('Enter a new title for the discussion:', discussionToEdit.title || '');
    if (newTitle === null) {
      // User cancelled the prompt
      return;
    }
    const trimmedTitle = newTitle.trim();
    if (trimmedTitle === '') {
      alert('Title cannot be empty.');
      return;
    }
    try {
      setBusy(true);
      const body = {
        chatId: discussionToEdit.chatId,
        title: trimmedTitle
      };
      const handleTokenUpdate = newToken => {
        setRestNonce(newToken);
        restNonceRef.current = newToken;
        _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_0__["default"].setToken(newToken); // Update globally
      };
      const response = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_1__.mwaiFetch)(`${restUrl}/mwai-ui/v1/discussions/edit`, body, restNonceRef.current, false, undefined, handleTokenUpdate);
      const data = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_1__.mwaiHandleRes)(response, null, debugMode ? "DISCUSSIONS" : null, handleTokenUpdate, debugMode);
      if (!data.success) {
        throw new Error(`Could not update the discussion: ${data.message}`);
      }

      // Update the discussions state
      setDiscussions(prevDiscussions => prevDiscussions.map(disc => disc.chatId === discussionToEdit.chatId ? {
        ...disc,
        title: trimmedTitle
      } : disc));
    } catch (err) {
      console.error(err);
      alert('An error occurred while updating the discussion.');
    } finally {
      setBusy(false);
    }
  };
  const onDeleteDiscussion = async discussionToDelete => {
    const confirmed = confirm('Are you sure you want to delete this discussion?');
    if (!confirmed) {
      return;
    }
    try {
      setBusy(true);
      const body = {
        chatIds: [discussionToDelete.chatId]
      };
      const handleTokenUpdate = newToken => {
        setRestNonce(newToken);
        restNonceRef.current = newToken;
        _app_helpers_tokenManager__WEBPACK_IMPORTED_MODULE_0__["default"].setToken(newToken); // Update globally
      };
      const response = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_1__.mwaiFetch)(`${restUrl}/mwai-ui/v1/discussions/delete`, body, restNonceRef.current, false, undefined, handleTokenUpdate);
      const data = await (0,_app_helpers__WEBPACK_IMPORTED_MODULE_1__.mwaiHandleRes)(response, null, debugMode ? "DISCUSSIONS" : null, handleTokenUpdate, debugMode);
      if (!data.success) {
        throw new Error(`Could not delete the discussion: ${data.message}`);
      }

      // Update the discussions state
      setDiscussions(prevDiscussions => prevDiscussions.filter(disc => disc.chatId !== discussionToDelete.chatId));

      // If the deleted discussion was selected, deselect it
      if ((discussion === null || discussion === void 0 ? void 0 : discussion.chatId) === discussionToDelete.chatId) {
        setDiscussion(null);
        setCurrentChatId(null);
      }

      // Check if this was the last item on the current page
      if (discussions.length === 1 && currentPage > 0) {
        // Go back to previous page
        const newPage = currentPage - 1;
        setCurrentPage(newPage);
        refresh(false, newPage, true);
      } else {
        // Refresh current page to update the list
        refresh(false, currentPage, true);
      }
    } catch (err) {
      console.error(err);
      alert('An error occurred while deleting the discussion.');
    } finally {
      setBusy(false);
    }
  };
  const onNewChatClick = async () => {
    // Don't add the new chat to the discussions list
    // Just clear the current discussion and let it be created when the first message is sent
    const chatbot = getChatbot(botId);
    const newChatId = (0,_app_helpers__WEBPACK_IMPORTED_MODULE_1__.randomStr)();
    chatbot.clear({
      chatId: newChatId
    });

    // Set discussion to null but track the chatId
    setDiscussion(null);
    setCurrentChatId(newChatId);
  };
  const actions = {
    onDiscussionClick,
    onNewChatClick,
    onEditDiscussion,
    onDeleteDiscussion,
    refresh,
    setCurrentPage
  };
  const state = {
    botId,
    pluginUrl,
    busy,
    setBusy,
    cssVariables,
    discussions,
    discussion,
    theme,
    hasEmptyDiscussion,
    currentPage,
    totalCount,
    system,
    paginationBusy
  };
  return /*#__PURE__*/React.createElement(DiscussionsContext.Provider, {
    value: {
      state,
      actions
    }
  }, children);
};

/***/ }),

/***/ "./app/js/chatbot/DiscussionsSystem.js":
/*!*********************************************!*\
  !*** ./app/js/chatbot/DiscussionsSystem.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _app_chatbot_DiscussionsContext__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/chatbot/DiscussionsContext */ "./app/js/chatbot/DiscussionsContext.js");
/* harmony import */ var _app_chatbot_DiscussionsUI__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @app/chatbot/DiscussionsUI */ "./app/js/chatbot/DiscussionsUI.js");


const DiscussionsSystem = props => {
  return /*#__PURE__*/React.createElement(_app_chatbot_DiscussionsContext__WEBPACK_IMPORTED_MODULE_0__.DiscussionsContextProvider, props, /*#__PURE__*/React.createElement(_app_chatbot_DiscussionsUI__WEBPACK_IMPORTED_MODULE_1__["default"], props));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (DiscussionsSystem);

/***/ }),

/***/ "./app/js/chatbot/DiscussionsUI.js":
/*!*****************************************!*\
  !*** ./app/js/chatbot/DiscussionsUI.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/pencil.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/trash.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/calendar.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/clock.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/message-square.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/ellipsis.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/refresh-cw.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/loader-circle.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/chevron-left.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/chevron-right.js");
/* harmony import */ var _app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/chatbot/helpers */ "./app/js/chatbot/helpers.js");
/* harmony import */ var _app_chatbot_DiscussionsContext__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @app/chatbot/DiscussionsContext */ "./app/js/chatbot/DiscussionsContext.js");
/* harmony import */ var _app_components_ContextMenu__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @app/components/ContextMenu */ "./app/js/components/ContextMenu.js");
/* harmony import */ var _app_chatbot_MwaiAPI__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @app/chatbot/MwaiAPI */ "./app/js/chatbot/MwaiAPI.js");
// React & Vendor Libs
const {
  useMemo,
  useEffect,
  useState,
  useCallback,
  useRef
} = wp.element;





const Discussion = ({
  discussion,
  onClick = () => {},
  selected = false,
  onEdit = () => {},
  onDelete = () => {},
  theme,
  system
}) => {
  var _system$metadata, _discussion$metadata_, _discussion$metadata_2, _discussion$metadata_3;
  const css = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.useClasses)();
  const [menuOpen, setMenuOpen] = useState(false);
  const menuButtonRef = useRef(null);
  const messages = discussion.messages;
  const message = messages[messages.length - 1];
  const preview = useMemo(() => {
    if (discussion.title) {
      return discussion.title;
    }
    const messageText = (message === null || message === void 0 ? void 0 : message.content.length) > 64 ? message.content.substring(0, 64) + '...' : message.content;
    return messageText || 'No messages yet';
  }, [discussion, message]);
  const baseClasses = css('mwai-discussion', {
    'mwai-active': selected
  });
  const onMenuClick = useCallback(e => {
    e.stopPropagation();
    setMenuOpen(!menuOpen);
  }, [menuOpen]);
  const onRenameClick = useCallback(() => {
    setMenuOpen(false);
    onEdit(discussion);
  }, [discussion, onEdit]);
  const onDeleteClick = useCallback(() => {
    setMenuOpen(false);
    onDelete(discussion);
  }, [discussion, onDelete]);

  // Don't memoize menuItems so filters are applied on every menu open
  const menuItems = (() => {
    const defaultItems = [{
      id: 'rename',
      icon: lucide_react__WEBPACK_IMPORTED_MODULE_1__["default"],
      label: 'Rename',
      onClick: onRenameClick,
      className: 'mwai-menu-item'
    }, {
      id: 'delete',
      icon: lucide_react__WEBPACK_IMPORTED_MODULE_2__["default"],
      label: 'Delete',
      onClick: onDeleteClick,
      className: 'mwai-menu-item mwai-danger'
    }];
    return (0,_app_chatbot_MwaiAPI__WEBPACK_IMPORTED_MODULE_3__.applyFilters)('mwai_discussion_menu_items', defaultItems, discussion);
  })();
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("li", {
    className: baseClasses,
    onClick: onClick
  }, /*#__PURE__*/React.createElement("div", {
    className: css('mwai-discussion-content')
  }, /*#__PURE__*/React.createElement("span", {
    className: css('mwai-discussion-title')
  }, preview), (system === null || system === void 0 || (_system$metadata = system.metadata) === null || _system$metadata === void 0 ? void 0 : _system$metadata.enabled) && /*#__PURE__*/React.createElement("div", {
    className: css('mwai-discussion-info')
  }, system.metadata.startDate && /*#__PURE__*/React.createElement("span", {
    className: css('mwai-info-item')
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_4__["default"], {
    size: 12
  }), /*#__PURE__*/React.createElement("span", null, ((_discussion$metadata_ = discussion.metadata_display) === null || _discussion$metadata_ === void 0 ? void 0 : _discussion$metadata_.start_date) || discussion.created)), system.metadata.lastUpdate && /*#__PURE__*/React.createElement("span", {
    className: css('mwai-info-item')
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_5__["default"], {
    size: 12
  }), /*#__PURE__*/React.createElement("span", null, ((_discussion$metadata_2 = discussion.metadata_display) === null || _discussion$metadata_2 === void 0 ? void 0 : _discussion$metadata_2.last_update) || discussion.updated)), system.metadata.messageCount && /*#__PURE__*/React.createElement("span", {
    className: css('mwai-info-item')
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_6__["default"], {
    size: 12
  }), /*#__PURE__*/React.createElement("span", null, ((_discussion$metadata_3 = discussion.metadata_display) === null || _discussion$metadata_3 === void 0 ? void 0 : _discussion$metadata_3.message_count) || messages.length)))), /*#__PURE__*/React.createElement("div", {
    className: css('mwai-discussion-actions')
  }, /*#__PURE__*/React.createElement("div", {
    ref: menuButtonRef,
    className: css('mwai-menu-icon'),
    onClick: onMenuClick
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_7__["default"], {
    size: 18
  })))), /*#__PURE__*/React.createElement(_app_components_ContextMenu__WEBPACK_IMPORTED_MODULE_8__["default"], {
    isOpen: menuOpen,
    anchorEl: menuButtonRef.current,
    onClose: () => setMenuOpen(false),
    menuItems: menuItems,
    theme: theme,
    context: discussion
  }));
};
const DiscussionsUI = props => {
  const {
    theme,
    style,
    params
  } = props;
  const css = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.useClasses)();
  const themeStyle = useMemo(() => (theme === null || theme === void 0 ? void 0 : theme.type) === 'css' ? theme === null || theme === void 0 ? void 0 : theme.style : null, [theme]);
  const {
    state,
    actions
  } = (0,_app_chatbot_DiscussionsContext__WEBPACK_IMPORTED_MODULE_9__.useDiscussionsContext)();
  const {
    botId,
    cssVariables,
    discussions,
    discussion,
    busy,
    hasEmptyDiscussion,
    currentPage,
    totalCount,
    system,
    paginationBusy
  } = state;
  const {
    onDiscussionClick,
    onNewChatClick,
    onEditDiscussion,
    onDeleteDiscussion,
    refresh,
    setCurrentPage
  } = actions;
  const {
    textNewChat
  } = params;
  useEffect(() => {
    // Prepare the API
    // mwaiAPI.open = () => setOpen(true);
    // mwaiAPI.close = () => setOpen(false);
    // mwaiAPI.toggle = () => setOpen(!open);
  });
  const baseClasses = css('mwai-discussions', {
    [`mwai-${theme === null || theme === void 0 ? void 0 : theme.themeId}-theme`]: true
  });
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
    id: `mwai-discussions-${botId}`,
    className: baseClasses,
    style: {
      ...cssVariables,
      ...style
    }
  }, themeStyle && /*#__PURE__*/React.createElement("style", null, themeStyle), /*#__PURE__*/React.createElement("div", {
    className: css('mwai-header')
  }, /*#__PURE__*/React.createElement("button", {
    onClick: () => onNewChatClick(),
    disabled: busy || hasEmptyDiscussion
  }, /*#__PURE__*/React.createElement("span", null, textNewChat ?? '+ New chat')), (system === null || system === void 0 ? void 0 : system.refreshInterval) === -1 && /*#__PURE__*/React.createElement("button", {
    className: css('mwai-refresh-btn'),
    onClick: () => refresh(),
    disabled: busy
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_10__["default"], {
    size: 16
  }))), /*#__PURE__*/React.createElement("div", {
    className: css('mwai-content'),
    style: {
      position: 'relative'
    }
  }, paginationBusy && /*#__PURE__*/React.createElement("div", {
    className: css('mwai-loading-overlay')
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_11__["default"], {
    size: 24,
    className: css('mwai-spinner')
  })), /*#__PURE__*/React.createElement("ul", {
    style: {
      listStyle: 'none',
      padding: 0,
      margin: 0
    }
  }, discussions.map(x => /*#__PURE__*/React.createElement(Discussion, {
    key: x.id,
    discussion: x,
    selected: (discussion === null || discussion === void 0 ? void 0 : discussion.id) === x.id,
    onClick: () => onDiscussionClick(x.chatId),
    onEdit: onEditDiscussion,
    onDelete: onDeleteDiscussion,
    theme: theme,
    system: system
  })))), (system === null || system === void 0 ? void 0 : system.paging) > 0 && totalCount > system.paging && /*#__PURE__*/React.createElement("div", {
    className: css('mwai-pagination')
  }, /*#__PURE__*/React.createElement("button", {
    onClick: () => {
      const newPage = currentPage - 1;
      setCurrentPage(newPage);
      refresh(false, newPage, true);
    },
    disabled: currentPage === 0 || busy || paginationBusy
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_12__["default"], {
    size: 16
  })), /*#__PURE__*/React.createElement("span", {
    className: css('mwai-page-indicator')
  }, `Page ${currentPage + 1} of ${Math.ceil(totalCount / system.paging)}`), /*#__PURE__*/React.createElement("button", {
    onClick: () => {
      const newPage = currentPage + 1;
      setCurrentPage(newPage);
      refresh(false, newPage, true);
    },
    disabled: currentPage >= Math.ceil(totalCount / system.paging) - 1 || busy || paginationBusy
  }, /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_13__["default"], {
    size: 16
  })))));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (DiscussionsUI);

/***/ }),

/***/ "./app/js/chatbot/MwaiAPI.js":
/*!***********************************!*\
  !*** ./app/js/chatbot/MwaiAPI.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   addAction: () => (/* binding */ addAction),
/* harmony export */   addFilter: () => (/* binding */ addFilter),
/* harmony export */   applyFilters: () => (/* binding */ applyFilters),
/* harmony export */   doAction: () => (/* binding */ doAction),
/* harmony export */   mwaiAPI: () => (/* binding */ mwaiAPI)
/* harmony export */ });
class MwaiAPI {
  constructor() {
    if (typeof window !== 'undefined' && window.MwaiAPI) {
      return window.MwaiAPI;
    }
    this.chatbots = [];
    this.forms = [];
    this.filters = {};
    this.actions = {};
    if (typeof window !== 'undefined') {
      window.MwaiAPI = this;
    }
  }
  getChatbot(botId = null) {
    if (!botId) {
      return this.chatbots[0];
    }
    return this.chatbots.find(x => x.botId === botId || x.customId === botId);
  }
  getForm(formId = null) {
    if (!formId) {
      return this.forms[0];
    }
    return this.forms.find(f => f.formId === formId);
  }
  addFilter(tag, callback, priority = 10) {
    if (!this.filters[tag]) {
      this.filters[tag] = [];
    }
    this.filters[tag].push({
      callback,
      priority
    });
    this.filters[tag].sort((a, b) => a.priority - b.priority);
  }
  applyFilters(tag, value, ...args) {
    if (!this.filters[tag]) {
      return value;
    }
    return this.filters[tag].reduce((acc, filter) => {
      return filter.callback(acc, ...args);
    }, value);
  }
  addAction(tag, callback, priority = 10) {
    if (!this.actions[tag]) {
      this.actions[tag] = [];
    }
    this.actions[tag].push({
      callback,
      priority
    });
    this.actions[tag].sort((a, b) => a.priority - b.priority);
  }
  doAction(tag, ...args) {
    if (!this.actions[tag]) {
      return;
    }
    this.actions[tag].forEach(action => {
      action.callback(...args);
    });
  }
}

// Ensure the class is only initialized once
const getInstance = () => {
  if (typeof window !== 'undefined' && window.MwaiAPI) {
    return window.MwaiAPI;
  }
  const instance = new MwaiAPI();
  if (typeof window !== 'undefined') {
    window.MwaiAPI = instance;
  }
  return instance;
};
const mwaiAPI = getInstance();
const addFilter = (tag, callback, priority = 10) => {
  mwaiAPI.addFilter(tag, callback, priority);
};
const applyFilters = (tag, value, ...args) => {
  return mwaiAPI.applyFilters(tag, value, ...args);
};
const addAction = (tag, callback, priority = 10) => {
  mwaiAPI.addAction(tag, callback, priority);
};
const doAction = (tag, ...args) => {
  mwaiAPI.doAction(tag, ...args);
};


/***/ }),

/***/ "./app/js/chatbot/helpers.js":
/*!***********************************!*\
  !*** ./app/js/chatbot/helpers.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Microphone: () => (/* binding */ Microphone),
/* harmony export */   TransitionBlock: () => (/* binding */ TransitionBlock),
/* harmony export */   doPlaceholders: () => (/* binding */ doPlaceholders),
/* harmony export */   isURL: () => (/* binding */ isURL),
/* harmony export */   processParameters: () => (/* binding */ processParameters),
/* harmony export */   useChrono: () => (/* binding */ useChrono),
/* harmony export */   useClasses: () => (/* binding */ useClasses),
/* harmony export */   useInterval: () => (/* binding */ useInterval),
/* harmony export */   useSpeechRecognition: () => (/* binding */ useSpeechRecognition),
/* harmony export */   useViewport: () => (/* binding */ useViewport)
/* harmony export */ });
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lucide-react */ "./node_modules/lucide-react/dist/esm/icons/mic.js");
function _extends() { _extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }
// React & Vendor Libs
const {
  useState,
  useMemo,
  useEffect,
  useRef,
  useCallback
} = wp.element;

const Microphone = ({
  active,
  disabled,
  ...rest
}) => {
  return (
    /*#__PURE__*/
    // eslint-disable-next-line react/no-unknown-property
    React.createElement("div", _extends({
      active: active ? "true" : "false",
      disabled: disabled
    }, rest), /*#__PURE__*/React.createElement(lucide_react__WEBPACK_IMPORTED_MODULE_0__["default"], {
      size: "24"
    }))
  );
};
function useInterval(delay, callback, enabled = true) {
  const savedCallback = useRef();
  useEffect(() => {
    savedCallback.current = callback;
  }, [callback]);
  useEffect(() => {
    function tick() {
      savedCallback.current();
    }
    if (delay !== null && enabled) {
      const id = setInterval(tick, delay);
      return () => clearInterval(id);
    }
  }, [delay, enabled]);
}
const useClasses = () => {
  return useMemo(() => {
    return (classNames, conditionalClasses) => {
      if (!Array.isArray(classNames)) {
        classNames = [classNames];
      }
      if (conditionalClasses) {
        Object.entries(conditionalClasses).forEach(([className, condition]) => {
          if (condition) {
            classNames.push(className);
          }
        });
      }
      return classNames.join(' ');
    };
  }, []);
};
function isURL(url) {
  if (!url || typeof url !== 'string') return false;
  return url.indexOf('http') === 0;
}
function useChrono() {
  const [timeElapsed, setTimeElapsed] = useState(null);
  const intervalIdRef = useRef(null);
  function startChrono() {
    if (intervalIdRef.current !== null) return;
    const startTime = Date.now();
    intervalIdRef.current = setInterval(() => {
      const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
      setTimeElapsed(formatTime(elapsedSeconds));
    }, 500);
  }
  function stopChrono() {
    clearInterval(intervalIdRef.current);
    intervalIdRef.current = null;
    setTimeElapsed(null);
  }
  function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
  }
  useEffect(() => {
    return () => {
      clearInterval(intervalIdRef.current);
    };
  }, []);
  return {
    timeElapsed,
    startChrono,
    stopChrono
  };
}
const doPlaceholders = (text, placeholders) => {
  if (typeof text !== 'string' || !placeholders) {
    return text;
  }
  Object.entries(placeholders).forEach(([key, value]) => {
    text = text.replace(new RegExp(`{${key}}`, 'g'), value);
  });
  return text;
};
const processParameters = (params, placeholders = []) => {
  var _params$guestName, _params$textSend, _params$textClear, _params$textInputPlac, _params$textComplianc, _params$icon, _params$iconText, _params$iconAlt, _params$iconPosition, _params$aiName, _params$userName, _params$aiAvatarUrl, _params$userAvatarUrl, _params$guestAvatarUr, _params$mode;
  const guestName = ((_params$guestName = params.guestName) === null || _params$guestName === void 0 ? void 0 : _params$guestName.trim()) ?? "";
  const textSend = ((_params$textSend = params.textSend) === null || _params$textSend === void 0 ? void 0 : _params$textSend.trim()) ?? "";
  const textClear = ((_params$textClear = params.textClear) === null || _params$textClear === void 0 ? void 0 : _params$textClear.trim()) ?? "";
  const textInputMaxLength = parseInt(params.textInputMaxLength);
  const textInputPlaceholder = ((_params$textInputPlac = params.textInputPlaceholder) === null || _params$textInputPlac === void 0 ? void 0 : _params$textInputPlac.trim()) ?? "";
  let textCompliance = ((_params$textComplianc = params.textCompliance) === null || _params$textComplianc === void 0 ? void 0 : _params$textComplianc.trim()) ?? "";
  let headerSubtitle = "";
  const window = Boolean(params.window);
  const copyButton = Boolean(params.copyButton);
  const fullscreen = Boolean(params.fullscreen);
  const icon = ((_params$icon = params.icon) === null || _params$icon === void 0 ? void 0 : _params$icon.trim()) ?? "";
  let iconText = ((_params$iconText = params.iconText) === null || _params$iconText === void 0 ? void 0 : _params$iconText.trim()) ?? "";
  const iconTextDelay = parseInt(params.iconTextDelay || 1);
  const iconAlt = ((_params$iconAlt = params.iconAlt) === null || _params$iconAlt === void 0 ? void 0 : _params$iconAlt.trim()) ?? "";
  const iconPosition = ((_params$iconPosition = params.iconPosition) === null || _params$iconPosition === void 0 ? void 0 : _params$iconPosition.trim()) ?? "";
  const iconBubble = Boolean(params.iconBubble);
  const aiName = ((_params$aiName = params.aiName) === null || _params$aiName === void 0 ? void 0 : _params$aiName.trim()) ?? "";
  const userName = ((_params$userName = params.userName) === null || _params$userName === void 0 ? void 0 : _params$userName.trim()) ?? "";
  const aiAvatar = Boolean(params === null || params === void 0 ? void 0 : params.aiAvatar);
  const userAvatar = Boolean(params === null || params === void 0 ? void 0 : params.userAvatar);
  const guestAvatar = Boolean(params === null || params === void 0 ? void 0 : params.guestAvatar);
  const aiAvatarUrl = aiAvatar ? (params === null || params === void 0 || (_params$aiAvatarUrl = params.aiAvatarUrl) === null || _params$aiAvatarUrl === void 0 ? void 0 : _params$aiAvatarUrl.trim()) ?? "" : null;
  const userAvatarUrl = userAvatar ? (params === null || params === void 0 || (_params$userAvatarUrl = params.userAvatarUrl) === null || _params$userAvatarUrl === void 0 ? void 0 : _params$userAvatarUrl.trim()) ?? "" : null;
  const guestAvatarUrl = guestAvatar ? (params === null || params === void 0 || (_params$guestAvatarUr = params.guestAvatarUrl) === null || _params$guestAvatarUr === void 0 ? void 0 : _params$guestAvatarUr.trim()) ?? "" : null;
  const localMemory = Boolean(params.localMemory);
  const imageUpload = Boolean(params.imageUpload);
  const fileUpload = Boolean(params.fileUpload);
  const fileSearch = Boolean(params.fileSearch);
  const mode = ((_params$mode = params.mode) === null || _params$mode === void 0 ? void 0 : _params$mode.trim()) ?? "chat";
  if (params.headerSubtitle === null || params.headerSubtitle === undefined) {
    headerSubtitle = "Discuss with";
  } else {
    var _params$headerSubtitl;
    headerSubtitle = ((_params$headerSubtitl = params.headerSubtitle) === null || _params$headerSubtitl === void 0 ? void 0 : _params$headerSubtitl.trim()) ?? "";
  }

  // This is also executed on the PHP-side, but having this here allows for easier testing in the WP Admin
  if (placeholders) {
    textCompliance = doPlaceholders(textCompliance, placeholders);
    iconText = doPlaceholders(iconText, placeholders);
  }
  return {
    textSend,
    textClear,
    textInputMaxLength,
    textInputPlaceholder,
    textCompliance,
    mode,
    window,
    copyButton,
    fullscreen,
    localMemory,
    imageUpload,
    fileUpload,
    fileSearch,
    icon,
    iconText,
    iconTextDelay,
    iconAlt,
    iconPosition,
    iconBubble,
    headerSubtitle,
    aiName,
    userName,
    guestName,
    aiAvatar,
    userAvatar,
    guestAvatar,
    aiAvatarUrl,
    userAvatarUrl,
    guestAvatarUrl
  };
};
const isAndroid = () => {
  return navigator.userAgent.toLowerCase().indexOf("android") > -1;
};
const useSpeechRecognition = onResult => {
  const [isListening, setIsListening] = useState(false);
  const [speechRecognitionAvailable, setSpeechRecognitionAvailable] = useState(false);
  useEffect(() => {
    if (typeof window !== 'undefined' && ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window)) {
      setSpeechRecognitionAvailable(true);
    }
  }, []);
  useEffect(() => {
    if (!speechRecognitionAvailable) {
      return;
    }
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const recognition = new SpeechRecognition();
    let handleResult = null;
    if (!isAndroid()) {
      recognition.interimResults = true;
      recognition.continuous = true;
      handleResult = event => {
        const transcript = Array.from(event.results).map(result => result[0]).map(result => result.transcript).join('');
        onResult(transcript);
      };
    } else {
      recognition.interimResults = false;
      recognition.continuous = false;
      handleResult = event => {
        const finalTranscript = Array.from(event.results).filter(result => result.isFinal).map(result => result[0].transcript).join('');
        onResult(finalTranscript);
        setIsListening(false);
      };
    }
    if (isListening) {
      recognition.addEventListener('result', handleResult);
      recognition.start();
    } else {
      recognition.removeEventListener('result', handleResult);
      recognition.abort();
    }
    return () => {
      recognition.abort();
    };
  }, [isListening, speechRecognitionAvailable]);
  return {
    isListening,
    setIsListening,
    speechRecognitionAvailable
  };
};
const TransitionBlock = ({
  if: condition,
  className,
  disableTransition = false,
  children,
  ...rest
}) => {
  const [shouldRender, setShouldRender] = useState(false);
  const [animationClass, setAnimationClass] = useState('mwai-transition');
  useEffect(() => {
    if (disableTransition) {
      setShouldRender(condition);
    } else {
      if (condition) {
        setShouldRender(true);
        setTimeout(() => {
          setAnimationClass('mwai-transition mwai-transition-visible');
        }, 150);
      } else {
        setAnimationClass('mwai-transition');
      }
    }
  }, [condition, disableTransition]);
  const handleTransitionEnd = () => {
    if (animationClass === 'mwai-transition' && !disableTransition) {
      setShouldRender(false);
    }
  };
  return !shouldRender ? null : /*#__PURE__*/React.createElement("div", _extends({
    className: `${className} ${disableTransition ? '' : animationClass}`,
    onTransitionEnd: handleTransitionEnd
  }, rest), children);
};
const useViewport = () => {
  const [viewportHeight, setViewportHeight] = useState(window.visualViewport.height);
  const isAndroid = useMemo(() => /Android/.test(navigator.userAgent), []);
  const isIOS = useMemo(() => /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream, []);
  const viewport = useRef(window.visualViewport);
  const handleResize = useCallback(() => {
    setViewportHeight(viewport.current.height);
  }, []);
  useEffect(() => {
    const currentViewport = viewport.current;
    currentViewport.addEventListener('resize', handleResize);
    if (isIOS) {
      window.addEventListener('resize', handleResize);
      document.addEventListener('focusin', handleResize);
    } else {
      currentViewport.addEventListener('scroll', handleResize);
    }
    return () => {
      currentViewport.removeEventListener('resize', handleResize);
      if (isIOS) {
        window.removeEventListener('resize', handleResize);
        document.removeEventListener('focusin', handleResize);
      } else {
        currentViewport.removeEventListener('scroll', handleResize);
      }
    };
  }, [handleResize, isIOS]);
  return {
    viewportHeight,
    isIOS,
    isAndroid
  };
};


/***/ }),

/***/ "./app/js/components/ContextMenu.js":
/*!******************************************!*\
  !*** ./app/js/components/ContextMenu.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-dom */ "react-dom");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react_dom__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @app/chatbot/helpers */ "./app/js/chatbot/helpers.js");
// React & Vendor Libs
const {
  useEffect,
  useRef,
  useState
} = wp.element;


const ContextMenu = ({
  isOpen,
  anchorEl,
  onClose,
  menuItems = [],
  className = '',
  theme,
  context
}) => {
  const css = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_1__.useClasses)();
  const menuRef = useRef(null);
  const [position, setPosition] = useState({
    top: 0,
    left: 0
  });
  useEffect(() => {
    if (isOpen && anchorEl) {
      const rect = anchorEl.getBoundingClientRect();
      const menuWidth = 120; // Approximate menu width
      const menuHeight = 80; // Approximate menu height

      // Calculate position
      let top = rect.bottom + 4;
      let left = rect.right - menuWidth;

      // Adjust if menu would go off screen
      if (left < 0) left = rect.left;
      if (top + menuHeight > window.innerHeight) {
        top = rect.top - menuHeight - 4;
      }
      setPosition({
        top,
        left
      });
    }
  }, [isOpen, anchorEl]);
  useEffect(() => {
    const handleClickOutside = event => {
      if (menuRef.current && !menuRef.current.contains(event.target) && anchorEl && !anchorEl.contains(event.target)) {
        onClose();
      }
    };
    const handleEscape = event => {
      if (event.key === 'Escape') {
        onClose();
      }
    };
    if (isOpen) {
      document.addEventListener('mousedown', handleClickOutside);
      document.addEventListener('keydown', handleEscape);
      return () => {
        document.removeEventListener('mousedown', handleClickOutside);
        document.removeEventListener('keydown', handleEscape);
      };
    }
  }, [isOpen, onClose, anchorEl]);
  if (!isOpen) return null;
  const menuContent = /*#__PURE__*/React.createElement("div", {
    ref: menuRef,
    className: css('mwai-context-menu-portal', {
      [`mwai-${theme === null || theme === void 0 ? void 0 : theme.themeId}-theme`]: theme === null || theme === void 0 ? void 0 : theme.themeId
    }),
    style: {
      position: 'fixed',
      top: `${position.top}px`,
      left: `${position.left}px`,
      zIndex: 999999
    }
  }, /*#__PURE__*/React.createElement("div", {
    className: css('mwai-context-menu'),
    style: {
      minWidth: '120px',
      overflow: 'hidden'
    }
  }, menuItems.map((item, index) => {
    // Handle separator
    if (item.type === 'separator') {
      return /*#__PURE__*/React.createElement("div", {
        key: item.id || `separator-${index}`,
        className: css('mwai-menu-separator'),
        style: {
          height: '1px',
          margin: '4px 0',
          background: 'var(--mwai-backgroundPrimaryColor, rgba(0,0,0,0.1))'
        }
      });
    }

    // Handle title/header
    if (item.type === 'title') {
      return /*#__PURE__*/React.createElement("div", {
        key: item.id || `title-${index}`,
        className: css('mwai-menu-title'),
        style: {
          padding: '8px 12px',
          fontSize: '11px',
          fontWeight: 'bold',
          opacity: 0.7,
          textTransform: 'uppercase'
        },
        dangerouslySetInnerHTML: item.html ? {
          __html: item.html
        } : undefined
      }, !item.html && item.label);
    }

    // Handle regular menu item
    const Icon = item.icon;
    if (item.html) {
      return /*#__PURE__*/React.createElement("div", {
        key: item.id,
        className: css(item.className || 'mwai-menu-item'),
        onClick: () => {
          if (item.onClick) {
            item.onClick(context);
            onClose();
          }
        },
        style: item.style,
        dangerouslySetInnerHTML: {
          __html: item.html
        }
      });
    }
    return /*#__PURE__*/React.createElement("div", {
      key: item.id,
      className: css(item.className || 'mwai-menu-item'),
      onClick: () => {
        if (item.onClick) {
          item.onClick(context);
          onClose();
        }
      },
      style: item.style
    }, Icon && /*#__PURE__*/React.createElement(Icon, {
      size: 14
    }), /*#__PURE__*/React.createElement("span", null, item.label));
  })));

  // Create portal to render at document body level
  return /*#__PURE__*/(0,react_dom__WEBPACK_IMPORTED_MODULE_0__.createPortal)(menuContent, document.body);
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ContextMenu);

/***/ }),

/***/ "./app/js/components/ReplyActions.js":
/*!*******************************************!*\
  !*** ./app/js/components/ReplyActions.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/chatbot/helpers */ "./app/js/chatbot/helpers.js");
function _extends() { _extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

const {
  useState,
  useEffect,
  useRef,
  useCallback
} = wp.element;
const svgPathDefault = '<path d="M7 5a3 3 0 0 1 3-3h9a3 3 0 0 1 3 3v9a3 3 0 0 1-3 3h-2v2a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3v-9a3 3 0 0 1 3-3h2zm2 2h5a3 3 0 0 1 3 3v5h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-9a1 1 0 0 0-1 1zM5 9a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h9a1 1 0 0 0 1-1v-9a1 1 0 0 0-1-1z" />';
const svgPathSuccess = '<path d="M10.7673 18C10.3106 18 9.86749 17.8046 9.54432 17.4555L5.50694 13.1222C4.83102 12.3968 4.83102 11.2208 5.50694 10.4954C6.18287 9.76997 7.27871 9.76997 7.95505 10.4954L10.6794 13.4196L16.9621 5.63976C17.5874 4.86495 18.6832 4.78289 19.4031 5.45388C20.125 6.12487 20.2036 7.29638 19.5759 8.07391L12.0778 17.3589C11.7639 17.7475 11.3119 17.9801 10.8319 18C10.8087 18 10.788 18 10.7673 18Z" />';
const svgPathError = '<path d="M17.7623 17.7626C17.0831 18.4418 15.9549 18.416 15.244 17.705L5.79906 8.26012C5.08811 7.54917 5.0623 6.42098 5.74145 5.74183C6.4206 5.06267 7.54879 5.08849 8.25975 5.79944L17.7047 15.2443C18.4156 15.9553 18.4414 17.0835 17.7623 17.7626Z" /><path d="M17.5508 8.52848L8.52842 17.5509C7.84927 18.23 6.72108 18.2042 6.01012 17.4933C5.29917 16.7823 5.27336 15.6541 5.95251 14.975L14.9749 5.95257C15.6541 5.27342 16.7823 5.29923 17.4932 6.01019C18.2042 6.72114 18.23 7.84933 17.5508 8.52848Z" />';
const ReplyActions = ({
  enabled,
  content,
  children,
  className,
  ...rest
}) => {
  const css = (0,_app_chatbot_helpers__WEBPACK_IMPORTED_MODULE_0__.useClasses)();
  const [copyStatus, setCopyStatus] = useState('idle');
  const [hidden, setHidden] = useState(true);
  const timeoutRef = useRef(null);
  const hasEnteredRef = useRef(false);
  const onCopy = () => {
    try {
      navigator.clipboard.writeText(content);
      setCopyStatus('success');
    } catch (err) {
      setCopyStatus('error');
      console.warn('Not allowed to copy to clipboard. Make sure your website uses HTTPS.', {
        content
      });
    } finally {
      setTimeout(() => {
        setCopyStatus('idle');
      }, 2000);
    }
  };
  const handleMouseEnter = useCallback(() => {
    if (!hasEnteredRef.current) {
      hasEnteredRef.current = true;
      timeoutRef.current = setTimeout(() => {
        setHidden(false);
      }, 500);
    }
  }, []);
  const handleMouseLeave = useCallback(() => {
    if (timeoutRef.current) {
      clearTimeout(timeoutRef.current);
    }
    setHidden(true);
    hasEnteredRef.current = false;
  }, []);
  useEffect(() => {
    return () => {
      if (timeoutRef.current) {
        clearTimeout(timeoutRef.current);
      }
    };
  }, []);
  const svgPath = copyStatus === 'success' ? svgPathSuccess : copyStatus === 'error' ? svgPathError : svgPathDefault;
  return /*#__PURE__*/React.createElement("div", _extends({}, rest, {
    onMouseLeave: handleMouseLeave,
    onMouseEnter: handleMouseEnter,
    onMouseOver: handleMouseEnter
  }), /*#__PURE__*/React.createElement("span", {
    className: className
  }, children), /*#__PURE__*/React.createElement("div", {
    className: css('mwai-reply-actions', {
      'mwai-hidden': hidden
    })
  }, enabled && /*#__PURE__*/React.createElement("div", {
    className: "mwai-copy-button",
    onClick: onCopy
  }, /*#__PURE__*/React.createElement("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    dangerouslySetInnerHTML: {
      __html: svgPath
    }
  }))));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ReplyActions);

/***/ }),

/***/ "./app/js/constants/streamTypes.js":
/*!*****************************************!*\
  !*** ./app/js/constants/streamTypes.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   STREAM_TYPES: () => (/* binding */ STREAM_TYPES),
/* harmony export */   STREAM_VISIBILITY: () => (/* binding */ STREAM_VISIBILITY),
/* harmony export */   getDefaultVisibility: () => (/* binding */ getDefaultVisibility)
/* harmony export */ });
// Define streaming message types (matching PHP constants)
const STREAM_TYPES = {
  // Content types
  CONTENT: 'content',
  // Regular assistant message content
  THINKING: 'thinking',
  // AI reasoning/thinking process
  CODE: 'code',
  // Code block content

  // Tool/Function types
  TOOL_CALL: 'tool_call',
  // Starting a tool/function call
  TOOL_ARGS: 'tool_args',
  // Tool arguments (usually hidden)
  TOOL_RESULT: 'tool_result',
  // Tool execution result
  MCP_DISCOVERY: 'mcp_discovery',
  // MCP tools being discovered

  // Search/Generation types
  WEB_SEARCH: 'web_search',
  // Web search in progress
  FILE_SEARCH: 'file_search',
  // File search in progress
  IMAGE_GEN: 'image_gen',
  // Image generation in progress
  EMBEDDINGS: 'embeddings',
  // Embeddings operation

  // System types
  DEBUG: 'debug',
  // Debug information
  STATUS: 'status',
  // Status updates (queued, processing, etc.)
  ERROR: 'error',
  // Error messages
  WARNING: 'warning',
  // Warning messages
  TRANSCRIPT: 'transcript',
  // Audio transcriptions

  // Control types
  START: 'start',
  // Stream started
  END: 'end',
  // Stream completed
  HEARTBEAT: 'heartbeat' // Keep-alive ping
};

// Message visibility settings
const STREAM_VISIBILITY = {
  VISIBLE: 'visible',
  // Show to user
  HIDDEN: 'hidden',
  // Hide from user (debug only)
  COLLAPSED: 'collapsed' // Show collapsed/summary view
};

// Helper to determine default visibility for each type
const getDefaultVisibility = type => {
  const hiddenTypes = [STREAM_TYPES.TOOL_ARGS, STREAM_TYPES.DEBUG, STREAM_TYPES.HEARTBEAT];
  const collapsedTypes = [STREAM_TYPES.THINKING, STREAM_TYPES.MCP_DISCOVERY, STREAM_TYPES.STATUS];
  if (hiddenTypes.includes(type)) return STREAM_VISIBILITY.HIDDEN;
  if (collapsedTypes.includes(type)) return STREAM_VISIBILITY.COLLAPSED;
  return STREAM_VISIBILITY.VISIBLE;
};

/***/ }),

/***/ "./app/js/helpers.js":
/*!***************************!*\
  !*** ./app/js/helpers.js ***!
  \***************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BlinkingCursor: () => (/* binding */ BlinkingCursor),
/* harmony export */   OutputHandler: () => (/* binding */ OutputHandler),
/* harmony export */   isEmoji: () => (/* binding */ isEmoji),
/* harmony export */   mwaiFetch: () => (/* binding */ mwaiFetch),
/* harmony export */   mwaiFetchUpload: () => (/* binding */ mwaiFetchUpload),
/* harmony export */   mwaiHandleRes: () => (/* binding */ mwaiHandleRes),
/* harmony export */   nekoStringify: () => (/* binding */ nekoStringify),
/* harmony export */   randomStr: () => (/* binding */ randomStr)
/* harmony export */ });
/* harmony import */ var markdown_to_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! markdown-to-jsx */ "./node_modules/markdown-to-jsx/dist/index.modern.js");
// React & Vendor Libs
const {
  useMemo,
  useEffect,
  useState
} = wp.element;


// Those helpers don't used any external libraries except Markdown.
// They should be as light as possible.

// Can be used this way:
// const streamCallback = !stream ? null : (content) => {
// // The whole content !
// };
// const res = await mwaiFetch(`${restUrl}/mwai-ui/v1/chats/submit`, body, restNonce, stream);
// const data = await mwaiHandleRes(res, streamCallback, debugMode ? "CHATBOT" : null);
// // data contains { success: true, reply } or { success: false, message }

function nekoStringify(obj, space = null, ignoreCircular = true) {
  const cache = [];
  return JSON.stringify(obj, (key, value) => {
    if (typeof value === 'object' && value !== null) {
      if (cache.includes(value)) {
        if (!ignoreCircular) {
          console.warn('Circular reference found.', {
            key,
            value,
            cache,
            cacheIndex: cache.indexOf(value)
          });
          throw new Error('Circular reference found. Cancelled.');
        }
        return;
      }
      cache.push(value);
    }
    return value;
  }, space);
}
async function mwaiHandleRes(fetchRes, onStream, debugName = null, onTokenUpdate = null, debugMode = false) {
  // Regular
  if (!onStream) {
    try {
      const data = await fetchRes.json();
      if (debugName) {
        console.log(`[${debugName}] IN: `, data);
      }

      // Check for new token in response
      if (data.new_token) {
        // Log token update if debug mode is enabled
        if (debugMode) {
          console.log('[MWAI] Token refreshed!');
        }
        if (onTokenUpdate) {
          onTokenUpdate(data.new_token);
        }
      }
      return data;
    } catch (err) {
      console.error("Could not parse the regular response.", {
        err,
        data
      });
      return {
        success: false,
        message: "Could not parse the regular response."
      };
    }
  }

  // Stream
  const reader = fetchRes.body.getReader();
  const decoder = new TextDecoder('utf-8');
  let buffer = '';
  let decodedContent = '';
  while (true) {
    const {
      value,
      done
    } = await reader.read();
    buffer += decoder.decode(value, {
      stream: true
    });
    if (done) break;
    const lines = buffer.split('\n');
    for (let i = 0; i < lines.length - 1; i++) {
      if (lines[i].indexOf('data: ') !== 0) {
        continue;
      }
      const data = JSON.parse(lines[i].replace('data: ', ''));
      if (data['type'] === 'live') {
        if (debugName) {
          console.log(`[${debugName} STREAM] LIVE: `, data);
        }
        // Handle enhanced stream messages
        if (data.subtype) {
          // New enhanced message format - pass full data object
          onStream && onStream(decodedContent, data);
          // Only accumulate content for actual content messages
          if (data.subtype === 'content') {
            decodedContent += data.data;
          }
        } else {
          // Legacy format - accumulate and pass raw data
          decodedContent += data.data;
          onStream && onStream(decodedContent, data.data);
        }
      } else if (data['type'] === 'error') {
        try {
          if (debugName) {
            console.error(`[${debugName} STREAM] ERROR: `, data.data);
          }
          return {
            success: false,
            message: data.data
          };
        } catch (err) {
          console.error("Could not parse the 'error' stream.", {
            err,
            data
          });
          return {
            success: false,
            message: "Could not parse the 'error' stream."
          };
        }
      } else if (data['type'] === 'end') {
        try {
          const finalData = JSON.parse(data.data);
          if (debugName) {
            console.log(`[${debugName} STREAM] END: `, finalData);
          }

          // Check for new token in streaming response
          if (finalData.new_token) {
            // Log token update if debug mode is enabled
            if (debugMode) {
              console.log('[MWAI] Token refreshed!');
            }
            if (onTokenUpdate) {
              onTokenUpdate(finalData.new_token);
            }
          }
          return finalData;
        } catch (err) {
          console.error("Could not parse the 'end' stream.", {
            err,
            data
          });
          return {
            success: false,
            message: "Could not parse the 'end' stream."
          };
        }
      }
    }
    buffer = lines[lines.length - 1];
  }
  try {
    const finalData = JSON.parse(buffer);
    if (debugName) {
      console.log(`[${debugName} STREAM] IN: `, finalData);
    }
    return finalData;
  } catch (err) {
    console.error("Could not parse the buffer.", {
      err,
      buffer
    });
    return {
      success: false,
      message: "Could not parse the buffer."
    };
  }
}
async function mwaiFetch(url, body, restNonce, isStream, signal = undefined, onTokenUpdate = null) {
  const headers = {
    'Content-Type': 'application/json'
  };
  if (restNonce) {
    headers['X-WP-Nonce'] = restNonce;
  }
  if (isStream) {
    headers['Accept'] = 'text/event-stream';
  }
  const response = await fetch(`${url}`, {
    method: 'POST',
    headers,
    body: nekoStringify(body),
    credentials: 'same-origin',
    signal
  });

  // Check for authentication errors
  if (response.status === 403 || response.status === 401) {
    try {
      const errorData = await response.clone().json();
      if (errorData.code === 'rest_cookie_invalid_nonce' || errorData.code === 'rest_forbidden') {
        // Token has expired - user needs to refresh the page
        console.error('[MWAI] Authentication token has expired. Please refresh the page to continue.');
        throw new Error('Your session has expired. Please refresh the page to continue using AI Engine.');
      }
    } catch (e) {
      // If it's not our error format, continue with the original error
      if (e.message && e.message.includes('session has expired')) {
        throw e;
      }
    }
  }

  // For non-streaming responses, check for new token immediately
  if (!isStream && response.ok) {
    try {
      const clonedResponse = response.clone();
      const data = await clonedResponse.json();
      if (data.new_token && onTokenUpdate) {
        onTokenUpdate(data.new_token);
      }
    } catch (e) {
      // If parsing fails, continue normally
    }
  }
  return response;
}
async function mwaiFetchUpload(url, file, restNonce, onProgress, params = {}) {
  return new Promise((resolve, reject) => {
    const formData = new FormData();
    formData.append('file', file);
    for (const [key, value] of Object.entries(params)) {
      formData.append(key, value);
    }
    const xhr = new XMLHttpRequest();

    // Set up any headers here
    xhr.open('POST', url, true);
    if (restNonce) {
      xhr.setRequestHeader('X-WP-Nonce', restNonce);
    }

    // Handle progress events
    xhr.upload.onprogress = function (event) {
      if (event.lengthComputable && onProgress) {
        const percentComplete = event.loaded / event.total * 100;
        onProgress(percentComplete); // Call the onProgress callback
      }
    };
    xhr.onload = function () {
      if (xhr.status >= 200 && xhr.status < 300) {
        try {
          const jsonResponse = JSON.parse(xhr.responseText);
          resolve(jsonResponse);
        } catch (error) {
          reject({
            status: xhr.status,
            statusText: xhr.statusText,
            error: 'The server response is not valid JSON'
          });
        }
      } else {
        // Actually, the server might have returned the error message in JSON format, with a success and a message.
        // So we try to parse it.
        try {
          const jsonResponse = JSON.parse(xhr.responseText);
          reject({
            status: xhr.status,
            message: jsonResponse.message
          });
          return;
        } catch (error) {
          // Not a JSON, so we continue.
        }
        reject({
          status: xhr.status,
          statusText: xhr.statusText
        });
      }
    };
    xhr.onerror = function () {
      reject({
        status: xhr.status,
        statusText: xhr.statusText
      });
    };
    xhr.send(formData);
  });
}
function randomStr() {
  return Math.random().toString(36).substring(2);
}

/**
 * Simulates a blinking cursor.
 *
 * @component
 * @returns {JSX.Element} A styled span element that represents the cursor.
 */
const BlinkingCursor = () => {
  const [visible, setVisible] = useState(true);
  useEffect(() => {
    const timeout = setTimeout(() => {
      const timer = setInterval(() => {
        setVisible(v => !v);
      }, 500);
      return () => clearInterval(timer); // Clean up on component unmount
    }, 200);
    return () => clearTimeout(timeout);
  }, []);
  const cursorStyle = {
    opacity: visible ? 1 : 0,
    width: '1px',
    height: '1em',
    borderLeft: '8px solid',
    marginLeft: '2px'
  };
  return /*#__PURE__*/React.createElement("span", {
    style: cursorStyle
  });
};

/**
 * Handle the output from the AI, and display it in a proper way (markdown, streaming, etc).
 *
 * @component
 * @param {Object} props - Props passed to the component.
 * @param {string} props.content - Markdown content to be processed.
 * @param {string} props.error - Error messages, if any.
 * @param {boolean} props.isStreaming - A flag indicating if the content is streaming.
 * @param {string} props.baseClass - The base CSS class for styling the component, defaults to "mwai-output-handler".
 *
 * @returns {JSX.Element} A Markdown element with processed content and appropriate classes.
 */
const OutputHandler = props => {
  const {
    content,
    error,
    isStreaming,
    baseClass = "mwai-output-handler"
  } = props;
  const isError = !!error;
  let data = (isError ? error : content) ?? "";

  // Ensure this is encloded markdown
  const matches = (data.match(/```/g) || []).length;
  if (matches % 2 !== 0) {
    // if count is odd
    data += "\n```"; // add ``` at the end
  } else if (isStreaming) {
    data += "<BlinkingCursor />";
  }
  const classes = useMemo(() => {
    const freshClasses = [baseClass];
    if (error) {
      freshClasses.push('mwai-error');
    }
    return freshClasses;
  }, [error]);
  const markdownOptions = useMemo(() => {
    const options = {
      wrapper: 'div',
      forceWrapper: true,
      overrides: {
        BlinkingCursor: {
          component: BlinkingCursor
        },
        a: {
          props: {
            target: "_blank"
          }
        }
      }
    };
    return options;
  }, []);
  return /*#__PURE__*/React.createElement(markdown_to_jsx__WEBPACK_IMPORTED_MODULE_0__["default"], {
    options: markdownOptions,
    className: classes.join(' '),
    children: data
  });
};
const emojiRegex = /([\u2700-\u27BF]|[\uE000-\uF8FF]|[\uD800-\uDFFF]|[\uFE00-\uFE0F]|[\u1F100-\u1F1FF]|[\u1F200-\u1F2FF]|[\u1F300-\u1F5FF]|[\u1F600-\u1F64F]|[\u1F680-\u1F6FF]|[\u1F700-\u1F77F]|[\u1F780-\u1F7FF]|[\u1F800-\u1F8FF]|[\u1F900-\u1F9FF]|[\u1FA00-\u1FA6F])/;
function isEmoji(str) {
  return str && str.length === 2 && emojiRegex.test(str);
}


/***/ }),

/***/ "./app/js/helpers/RealtimeEventEmitter.js":
/*!************************************************!*\
  !*** ./app/js/helpers/RealtimeEventEmitter.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../constants/streamTypes */ "./app/js/constants/streamTypes.js");


/**
 * RealtimeEventEmitter - A clean helper for emitting stream events in the Realtime API
 * 
 * This class provides a simple interface for emitting stream events that are
 * compatible with the existing stream event system used in text-based chats.
 */
class RealtimeEventEmitter {
  constructor(onEvent, eventLogsEnabled = false) {
    this.onEvent = onEvent;
    this.eventLogsEnabled = eventLogsEnabled;
    this.sessionStartTime = null;
  }

  /**
   * Emit a stream event if event logs are enabled and callback is available
   */
  emit(subtype, data, metadata = {}) {
    if (!this.eventLogsEnabled || !this.onEvent) return;
    const event = {
      type: 'event',
      subtype,
      data,
      timestamp: new Date().getTime(),
      ...metadata
    };
    this.onEvent('', event);
  }

  // Session lifecycle events
  sessionStarting() {
    this.sessionStartTime = new Date().getTime();
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.STATUS, 'Starting realtime session...');
  }
  sessionConnected() {
    const duration = this.sessionStartTime ? new Date().getTime() - this.sessionStartTime : 0;
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.STATUS, `Realtime session connected in ${duration}ms.`);
  }
  sessionEnding() {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.STATUS, 'Ending realtime session...');
  }
  sessionError(error) {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.ERROR, `Realtime session error: ${error}`);
  }

  // Audio events
  userStartedSpeaking() {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.STATUS, 'User speaking...', {
      visibility: 'collapsed'
    });
  }
  userStoppedSpeaking() {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.STATUS, 'User finished speaking.', {
      visibility: 'collapsed'
    });
  }
  assistantStartedSpeaking() {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.STATUS, 'Assistant speaking...', {
      visibility: 'collapsed'
    });
  }
  assistantStoppedSpeaking() {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.STATUS, 'Assistant finished speaking.', {
      visibility: 'collapsed'
    });
  }

  // Function calling events
  functionCalling(name, args) {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.TOOL_CALL, `Calling ${name}...`, {
      metadata: {
        tool_name: name,
        arguments: args
      }
    });
  }
  functionResult(name, result) {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.TOOL_RESULT, `Got result from ${name}.`, {
      metadata: {
        tool_name: name,
        result
      }
    });
  }
  functionError(name, error) {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.ERROR, `Function ${name} failed: ${error}`, {
      metadata: {
        tool_name: name
      }
    });
  }

  // Transcription events
  userTranscribed(text) {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.STATUS, `User: "${text}"`, {
      visibility: 'collapsed'
    });
  }
  assistantTranscribed(text) {
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.STATUS, `Assistant: "${text}"`, {
      visibility: 'collapsed'
    });
  }

  // Statistics event
  usageUpdated(stats) {
    const {
      text_input_tokens,
      audio_input_tokens,
      text_output_tokens,
      audio_output_tokens
    } = stats;
    const total = text_input_tokens + audio_input_tokens + text_output_tokens + audio_output_tokens;
    this.emit(_constants_streamTypes__WEBPACK_IMPORTED_MODULE_0__.STREAM_TYPES.STATUS, `Tokens used: ${total} (Text: ${text_input_tokens}/${text_output_tokens}, Audio: ${audio_input_tokens}/${audio_output_tokens})`, {
      visibility: 'collapsed',
      metadata: {
        usage: stats
      }
    });
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (RealtimeEventEmitter);

/***/ }),

/***/ "./app/js/helpers/tokenManager.js":
/*!****************************************!*\
  !*** ./app/js/helpers/tokenManager.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
// Global token manager to share REST nonce across all components
class TokenManager {
  constructor() {
    this.token = null;
    this.listeners = new Set();
  }
  setToken(token) {
    if (this.token !== token) {
      this.token = token;
      this.notifyListeners();
    }
  }
  getToken() {
    return this.token;
  }
  subscribe(listener) {
    this.listeners.add(listener);
    return () => this.listeners.delete(listener);
  }
  notifyListeners() {
    this.listeners.forEach(listener => listener(this.token));
  }
}

// Create a singleton instance
const tokenManager = new TokenManager();

// Initialize with the global token if available
if (typeof window !== 'undefined' && window.mwai && window.mwai.rest_nonce) {
  tokenManager.setToken(window.mwai.rest_nonce);
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (tokenManager);

/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/Icon.js":
/*!****************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/Icon.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Icon)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _defaultAttributes_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./defaultAttributes.js */ "./node_modules/lucide-react/dist/esm/defaultAttributes.js");
/* harmony import */ var _shared_src_utils_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./shared/src/utils.js */ "./node_modules/lucide-react/dist/esm/shared/src/utils.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */





const Icon = (0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(
  ({
    color = "currentColor",
    size = 24,
    strokeWidth = 2,
    absoluteStrokeWidth,
    className = "",
    children,
    iconNode,
    ...rest
  }, ref) => {
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(
      "svg",
      {
        ref,
        ..._defaultAttributes_js__WEBPACK_IMPORTED_MODULE_1__["default"],
        width: size,
        height: size,
        stroke: color,
        strokeWidth: absoluteStrokeWidth ? Number(strokeWidth) * 24 / Number(size) : strokeWidth,
        className: (0,_shared_src_utils_js__WEBPACK_IMPORTED_MODULE_2__.mergeClasses)("lucide", className),
        ...rest
      },
      [
        ...iconNode.map(([tag, attrs]) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(tag, attrs)),
        ...Array.isArray(children) ? children : [children]
      ]
    );
  }
);


//# sourceMappingURL=Icon.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/createLucideIcon.js":
/*!****************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/createLucideIcon.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ createLucideIcon)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _shared_src_utils_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./shared/src/utils.js */ "./node_modules/lucide-react/dist/esm/shared/src/utils.js");
/* harmony import */ var _Icon_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Icon.js */ "./node_modules/lucide-react/dist/esm/Icon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */





const createLucideIcon = (iconName, iconNode) => {
  const Component = (0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(
    ({ className, ...props }, ref) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Icon_js__WEBPACK_IMPORTED_MODULE_1__["default"], {
      ref,
      iconNode,
      className: (0,_shared_src_utils_js__WEBPACK_IMPORTED_MODULE_2__.mergeClasses)(`lucide-${(0,_shared_src_utils_js__WEBPACK_IMPORTED_MODULE_2__.toKebabCase)(iconName)}`, className),
      ...props
    })
  );
  Component.displayName = `${iconName}`;
  return Component;
};


//# sourceMappingURL=createLucideIcon.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/defaultAttributes.js":
/*!*****************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/defaultAttributes.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ defaultAttributes)
/* harmony export */ });
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */

var defaultAttributes = {
  xmlns: "http://www.w3.org/2000/svg",
  width: 24,
  height: 24,
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  strokeWidth: 2,
  strokeLinecap: "round",
  strokeLinejoin: "round"
};


//# sourceMappingURL=defaultAttributes.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/activity.js":
/*!**************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/activity.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Activity)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Activity = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Activity", [
  [
    "path",
    {
      d: "M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2",
      key: "169zse"
    }
  ]
]);


//# sourceMappingURL=activity.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/brain.js":
/*!***********************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/brain.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Brain)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Brain = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Brain", [
  [
    "path",
    {
      d: "M12 5a3 3 0 1 0-5.997.125 4 4 0 0 0-2.526 5.77 4 4 0 0 0 .556 6.588A4 4 0 1 0 12 18Z",
      key: "l5xja"
    }
  ],
  [
    "path",
    {
      d: "M12 5a3 3 0 1 1 5.997.125 4 4 0 0 1 2.526 5.77 4 4 0 0 1-.556 6.588A4 4 0 1 1 12 18Z",
      key: "ep3f8r"
    }
  ],
  ["path", { d: "M15 13a4.5 4.5 0 0 1-3-4 4.5 4.5 0 0 1-3 4", key: "1p4c4q" }],
  ["path", { d: "M17.599 6.5a3 3 0 0 0 .399-1.375", key: "tmeiqw" }],
  ["path", { d: "M6.003 5.125A3 3 0 0 0 6.401 6.5", key: "105sqy" }],
  ["path", { d: "M3.477 10.896a4 4 0 0 1 .585-.396", key: "ql3yin" }],
  ["path", { d: "M19.938 10.5a4 4 0 0 1 .585.396", key: "1qfode" }],
  ["path", { d: "M6 18a4 4 0 0 1-1.967-.516", key: "2e4loj" }],
  ["path", { d: "M19.967 17.484A4 4 0 0 1 18 18", key: "159ez6" }]
]);


//# sourceMappingURL=brain.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/bug.js":
/*!*********************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/bug.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Bug)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Bug = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Bug", [
  ["path", { d: "m8 2 1.88 1.88", key: "fmnt4t" }],
  ["path", { d: "M14.12 3.88 16 2", key: "qol33r" }],
  ["path", { d: "M9 7.13v-1a3.003 3.003 0 1 1 6 0v1", key: "d7y7pr" }],
  [
    "path",
    {
      d: "M12 20c-3.3 0-6-2.7-6-6v-3a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v3c0 3.3-2.7 6-6 6",
      key: "xs1cw7"
    }
  ],
  ["path", { d: "M12 20v-9", key: "1qisl0" }],
  ["path", { d: "M6.53 9C4.6 8.8 3 7.1 3 5", key: "32zzws" }],
  ["path", { d: "M6 13H2", key: "82j7cp" }],
  ["path", { d: "M3 21c0-2.1 1.7-3.9 3.8-4", key: "4p0ekp" }],
  ["path", { d: "M20.97 5c0 2.1-1.6 3.8-3.5 4", key: "18gb23" }],
  ["path", { d: "M22 13h-4", key: "1jl80f" }],
  ["path", { d: "M17.2 17c2.1.1 3.8 1.9 3.8 4", key: "k3fwyw" }]
]);


//# sourceMappingURL=bug.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/calendar.js":
/*!**************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/calendar.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Calendar)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Calendar = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Calendar", [
  ["path", { d: "M8 2v4", key: "1cmpym" }],
  ["path", { d: "M16 2v4", key: "4m81vk" }],
  ["rect", { width: "18", height: "18", x: "3", y: "4", rx: "2", key: "1hopcy" }],
  ["path", { d: "M3 10h18", key: "8toen8" }]
]);


//# sourceMappingURL=calendar.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/captions.js":
/*!**************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/captions.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Captions)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Captions = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Captions", [
  ["rect", { width: "18", height: "14", x: "3", y: "5", rx: "2", ry: "2", key: "12ruh7" }],
  ["path", { d: "M7 15h4M15 15h2M7 11h2M13 11h4", key: "1ueiar" }]
]);


//# sourceMappingURL=captions.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/chevron-down.js":
/*!******************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/chevron-down.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ChevronDown)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const ChevronDown = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("ChevronDown", [
  ["path", { d: "m6 9 6 6 6-6", key: "qrunsl" }]
]);


//# sourceMappingURL=chevron-down.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/chevron-left.js":
/*!******************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/chevron-left.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ChevronLeft)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const ChevronLeft = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("ChevronLeft", [
  ["path", { d: "m15 18-6-6 6-6", key: "1wnfg3" }]
]);


//# sourceMappingURL=chevron-left.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/chevron-right.js":
/*!*******************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/chevron-right.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ChevronRight)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const ChevronRight = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("ChevronRight", [
  ["path", { d: "m9 18 6-6-6-6", key: "mthhwq" }]
]);


//# sourceMappingURL=chevron-right.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/circle-alert.js":
/*!******************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/circle-alert.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ CircleAlert)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const CircleAlert = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("CircleAlert", [
  ["circle", { cx: "12", cy: "12", r: "10", key: "1mglay" }],
  ["line", { x1: "12", x2: "12", y1: "8", y2: "12", key: "1pkeuh" }],
  ["line", { x1: "12", x2: "12.01", y1: "16", y2: "16", key: "4dfq90" }]
]);


//# sourceMappingURL=circle-alert.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/clock.js":
/*!***********************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/clock.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Clock)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Clock = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Clock", [
  ["circle", { cx: "12", cy: "12", r: "10", key: "1mglay" }],
  ["polyline", { points: "12 6 12 12 16 14", key: "68esgv" }]
]);


//# sourceMappingURL=clock.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/ellipsis.js":
/*!**************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/ellipsis.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Ellipsis)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Ellipsis = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Ellipsis", [
  ["circle", { cx: "12", cy: "12", r: "1", key: "41hilf" }],
  ["circle", { cx: "19", cy: "12", r: "1", key: "1wjl8i" }],
  ["circle", { cx: "5", cy: "12", r: "1", key: "1pcz8c" }]
]);


//# sourceMappingURL=ellipsis.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/eraser.js":
/*!************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/eraser.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Eraser)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Eraser = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Eraser", [
  [
    "path",
    {
      d: "m7 21-4.3-4.3c-1-1-1-2.5 0-3.4l9.6-9.6c1-1 2.5-1 3.4 0l5.6 5.6c1 1 1 2.5 0 3.4L13 21",
      key: "182aya"
    }
  ],
  ["path", { d: "M22 21H7", key: "t4ddhn" }],
  ["path", { d: "m5 11 9 9", key: "1mo9qw" }]
]);


//# sourceMappingURL=eraser.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/loader-circle.js":
/*!*******************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/loader-circle.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ LoaderCircle)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const LoaderCircle = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("LoaderCircle", [
  ["path", { d: "M21 12a9 9 0 1 1-6.219-8.56", key: "13zald" }]
]);


//# sourceMappingURL=loader-circle.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/loader.js":
/*!************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/loader.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Loader)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Loader = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Loader", [
  ["path", { d: "M12 2v4", key: "3427ic" }],
  ["path", { d: "m16.2 7.8 2.9-2.9", key: "r700ao" }],
  ["path", { d: "M18 12h4", key: "wj9ykh" }],
  ["path", { d: "m16.2 16.2 2.9 2.9", key: "1bxg5t" }],
  ["path", { d: "M12 18v4", key: "jadmvz" }],
  ["path", { d: "m4.9 19.1 2.9-2.9", key: "bwix9q" }],
  ["path", { d: "M2 12h4", key: "j09sii" }],
  ["path", { d: "m4.9 4.9 2.9 2.9", key: "giyufr" }]
]);


//# sourceMappingURL=loader.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/maximize-2.js":
/*!****************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/maximize-2.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Maximize2)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Maximize2 = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Maximize2", [
  ["polyline", { points: "15 3 21 3 21 9", key: "mznyad" }],
  ["polyline", { points: "9 21 3 21 3 15", key: "1avn1i" }],
  ["line", { x1: "21", x2: "14", y1: "3", y2: "10", key: "ota7mn" }],
  ["line", { x1: "3", x2: "10", y1: "21", y2: "14", key: "1atl0r" }]
]);


//# sourceMappingURL=maximize-2.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/message-square.js":
/*!********************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/message-square.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ MessageSquare)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const MessageSquare = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("MessageSquare", [
  ["path", { d: "M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z", key: "1lielz" }]
]);


//# sourceMappingURL=message-square.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/mic.js":
/*!*********************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/mic.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Mic)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Mic = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Mic", [
  ["path", { d: "M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z", key: "131961" }],
  ["path", { d: "M19 10v2a7 7 0 0 1-14 0v-2", key: "1vc78b" }],
  ["line", { x1: "12", x2: "12", y1: "19", y2: "22", key: "x3vr5v" }]
]);


//# sourceMappingURL=mic.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/minimize-2.js":
/*!****************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/minimize-2.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Minimize2)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Minimize2 = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Minimize2", [
  ["polyline", { points: "4 14 10 14 10 20", key: "11kfnr" }],
  ["polyline", { points: "20 10 14 10 14 4", key: "rlmsce" }],
  ["line", { x1: "14", x2: "21", y1: "10", y2: "3", key: "o5lafz" }],
  ["line", { x1: "3", x2: "10", y1: "21", y2: "14", key: "1atl0r" }]
]);


//# sourceMappingURL=minimize-2.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/pause.js":
/*!***********************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/pause.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Pause)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Pause = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Pause", [
  ["rect", { x: "14", y: "4", width: "4", height: "16", rx: "1", key: "zuxfzm" }],
  ["rect", { x: "6", y: "4", width: "4", height: "16", rx: "1", key: "1okwgv" }]
]);


//# sourceMappingURL=pause.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/pencil.js":
/*!************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/pencil.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Pencil)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Pencil = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Pencil", [
  [
    "path",
    {
      d: "M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z",
      key: "1a8usu"
    }
  ],
  ["path", { d: "m15 5 4 4", key: "1mk7zo" }]
]);


//# sourceMappingURL=pencil.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/play.js":
/*!**********************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/play.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Play)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Play = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Play", [
  ["polygon", { points: "6 3 20 12 6 21 6 3", key: "1oa8hb" }]
]);


//# sourceMappingURL=play.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/refresh-cw.js":
/*!****************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/refresh-cw.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ RefreshCw)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const RefreshCw = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("RefreshCw", [
  ["path", { d: "M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8", key: "v9h5vc" }],
  ["path", { d: "M21 3v5h-5", key: "1q7to0" }],
  ["path", { d: "M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16", key: "3uifl3" }],
  ["path", { d: "M8 16H3v5", key: "1cv678" }]
]);


//# sourceMappingURL=refresh-cw.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/search.js":
/*!************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/search.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Search)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Search = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Search", [
  ["circle", { cx: "11", cy: "11", r: "8", key: "4ej97u" }],
  ["path", { d: "m21 21-4.3-4.3", key: "1qie3q" }]
]);


//# sourceMappingURL=search.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/send.js":
/*!**********************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/send.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Send)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Send = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Send", [
  [
    "path",
    {
      d: "M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z",
      key: "1ffxy3"
    }
  ],
  ["path", { d: "m21.854 2.147-10.94 10.939", key: "12cjpa" }]
]);


//# sourceMappingURL=send.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/square.js":
/*!************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/square.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Square)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Square = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Square", [
  ["rect", { width: "18", height: "18", x: "3", y: "3", rx: "2", key: "afitv7" }]
]);


//# sourceMappingURL=square.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/trash.js":
/*!***********************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/trash.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Trash)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Trash = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Trash", [
  ["path", { d: "M3 6h18", key: "d0wm0j" }],
  ["path", { d: "M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6", key: "4alrt4" }],
  ["path", { d: "M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2", key: "v07s0e" }]
]);


//# sourceMappingURL=trash.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/users.js":
/*!***********************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/users.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Users)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Users = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Users", [
  ["path", { d: "M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2", key: "1yyitq" }],
  ["circle", { cx: "9", cy: "7", r: "4", key: "nufk8" }],
  ["path", { d: "M22 21v-2a4 4 0 0 0-3-3.87", key: "kshegd" }],
  ["path", { d: "M16 3.13a4 4 0 0 1 0 7.75", key: "1da9ce" }]
]);


//# sourceMappingURL=users.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/wrench.js":
/*!************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/wrench.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Wrench)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const Wrench = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("Wrench", [
  [
    "path",
    {
      d: "M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z",
      key: "cbrjhi"
    }
  ]
]);


//# sourceMappingURL=wrench.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/icons/x.js":
/*!*******************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/icons/x.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ X)
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const X = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("X", [
  ["path", { d: "M18 6 6 18", key: "1bl5f8" }],
  ["path", { d: "m6 6 12 12", key: "d8bk6v" }]
]);


//# sourceMappingURL=x.js.map


/***/ }),

/***/ "./node_modules/lucide-react/dist/esm/shared/src/utils.js":
/*!****************************************************************!*\
  !*** ./node_modules/lucide-react/dist/esm/shared/src/utils.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   mergeClasses: () => (/* binding */ mergeClasses),
/* harmony export */   toKebabCase: () => (/* binding */ toKebabCase)
/* harmony export */ });
/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */

const toKebabCase = (string) => string.replace(/([a-z0-9])([A-Z])/g, "$1-$2").toLowerCase();
const mergeClasses = (...classes) => classes.filter((className, index, array) => {
  return Boolean(className) && className.trim() !== "" && array.indexOf(className) === index;
}).join(" ").trim();


//# sourceMappingURL=utils.js.map


/***/ }),

/***/ "./node_modules/react-textarea-autosize/dist/react-textarea-autosize.browser.development.esm.js":
/*!******************************************************************************************************!*\
  !*** ./node_modules/react-textarea-autosize/dist/react-textarea-autosize.browser.development.esm.js ***!
  \******************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ index)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/react-textarea-autosize/node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutPropertiesLoose */ "./node_modules/react-textarea-autosize/node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var use_latest__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! use-latest */ "./node_modules/use-latest/dist/use-latest.esm.js");
/* harmony import */ var use_composed_ref__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! use-composed-ref */ "./node_modules/use-composed-ref/dist/use-composed-ref.esm.js");






var HIDDEN_TEXTAREA_STYLE = {
  'min-height': '0',
  'max-height': 'none',
  height: '0',
  visibility: 'hidden',
  overflow: 'hidden',
  position: 'absolute',
  'z-index': '-1000',
  top: '0',
  right: '0'
};
var forceHiddenStyles = function forceHiddenStyles(node) {
  Object.keys(HIDDEN_TEXTAREA_STYLE).forEach(function (key) {
    node.style.setProperty(key, HIDDEN_TEXTAREA_STYLE[key], 'important');
  });
};
var forceHiddenStyles$1 = forceHiddenStyles;

// TODO: use labelled tuples once they are avaiable:
//   export type CalculatedNodeHeights = [height: number, rowHeight: number];
// https://github.com/microsoft/TypeScript/issues/28259

var hiddenTextarea = null;
var getHeight = function getHeight(node, sizingData) {
  var height = node.scrollHeight;
  if (sizingData.sizingStyle.boxSizing === 'border-box') {
    // border-box: add border, since height = content + padding + border
    return height + sizingData.borderSize;
  }

  // remove padding, since height = content
  return height - sizingData.paddingSize;
};
function calculateNodeHeight(sizingData, value, minRows, maxRows) {
  if (minRows === void 0) {
    minRows = 1;
  }
  if (maxRows === void 0) {
    maxRows = Infinity;
  }
  if (!hiddenTextarea) {
    hiddenTextarea = document.createElement('textarea');
    hiddenTextarea.setAttribute('tabindex', '-1');
    hiddenTextarea.setAttribute('aria-hidden', 'true');
    forceHiddenStyles$1(hiddenTextarea);
  }
  if (hiddenTextarea.parentNode === null) {
    document.body.appendChild(hiddenTextarea);
  }
  var paddingSize = sizingData.paddingSize,
    borderSize = sizingData.borderSize,
    sizingStyle = sizingData.sizingStyle;
  var boxSizing = sizingStyle.boxSizing;
  Object.keys(sizingStyle).forEach(function (_key) {
    var key = _key;
    hiddenTextarea.style[key] = sizingStyle[key];
  });
  forceHiddenStyles$1(hiddenTextarea);
  hiddenTextarea.value = value;
  var height = getHeight(hiddenTextarea, sizingData);
  // Double set and calc due to Firefox bug: https://bugzilla.mozilla.org/show_bug.cgi?id=1795904
  hiddenTextarea.value = value;
  height = getHeight(hiddenTextarea, sizingData);

  // measure height of a textarea with a single row
  hiddenTextarea.value = 'x';
  var rowHeight = hiddenTextarea.scrollHeight - paddingSize;
  var minHeight = rowHeight * minRows;
  if (boxSizing === 'border-box') {
    minHeight = minHeight + paddingSize + borderSize;
  }
  height = Math.max(minHeight, height);
  var maxHeight = rowHeight * maxRows;
  if (boxSizing === 'border-box') {
    maxHeight = maxHeight + paddingSize + borderSize;
  }
  height = Math.min(maxHeight, height);
  return [height, rowHeight];
}

var noop = function noop() {};
var pick = function pick(props, obj) {
  return props.reduce(function (acc, prop) {
    acc[prop] = obj[prop];
    return acc;
  }, {});
};

var SIZING_STYLE = ['borderBottomWidth', 'borderLeftWidth', 'borderRightWidth', 'borderTopWidth', 'boxSizing', 'fontFamily', 'fontSize', 'fontStyle', 'fontWeight', 'letterSpacing', 'lineHeight', 'paddingBottom', 'paddingLeft', 'paddingRight', 'paddingTop',
// non-standard
'tabSize', 'textIndent',
// non-standard
'textRendering', 'textTransform', 'width', 'wordBreak'];
var isIE = !!document.documentElement.currentStyle ;
var getSizingData = function getSizingData(node) {
  var style = window.getComputedStyle(node);
  if (style === null) {
    return null;
  }
  var sizingStyle = pick(SIZING_STYLE, style);
  var boxSizing = sizingStyle.boxSizing;

  // probably node is detached from DOM, can't read computed dimensions
  if (boxSizing === '') {
    return null;
  }

  // IE (Edge has already correct behaviour) returns content width as computed width
  // so we need to add manually padding and border widths
  if (isIE && boxSizing === 'border-box') {
    sizingStyle.width = parseFloat(sizingStyle.width) + parseFloat(sizingStyle.borderRightWidth) + parseFloat(sizingStyle.borderLeftWidth) + parseFloat(sizingStyle.paddingRight) + parseFloat(sizingStyle.paddingLeft) + 'px';
  }
  var paddingSize = parseFloat(sizingStyle.paddingBottom) + parseFloat(sizingStyle.paddingTop);
  var borderSize = parseFloat(sizingStyle.borderBottomWidth) + parseFloat(sizingStyle.borderTopWidth);
  return {
    sizingStyle: sizingStyle,
    paddingSize: paddingSize,
    borderSize: borderSize
  };
};
var getSizingData$1 = getSizingData;

function useListener(target, type, listener) {
  var latestListener = (0,use_latest__WEBPACK_IMPORTED_MODULE_3__["default"])(listener);
  react__WEBPACK_IMPORTED_MODULE_2__.useLayoutEffect(function () {
    var handler = function handler(ev) {
      return latestListener.current(ev);
    };

    // might happen if document.fonts is not defined, for instance
    if (!target) {
      return;
    }
    target.addEventListener(type, handler);
    return function () {
      return target.removeEventListener(type, handler);
    };
  }, []);
}
var useWindowResizeListener = function useWindowResizeListener(listener) {
  useListener(window, 'resize', listener);
};
var useFontsLoadedListener = function useFontsLoadedListener(listener) {
  useListener(document.fonts, 'loadingdone', listener);
};

var _excluded = ["cacheMeasurements", "maxRows", "minRows", "onChange", "onHeightChange"];
var TextareaAutosize = function TextareaAutosize(_ref, userRef) {
  var cacheMeasurements = _ref.cacheMeasurements,
    maxRows = _ref.maxRows,
    minRows = _ref.minRows,
    _ref$onChange = _ref.onChange,
    onChange = _ref$onChange === void 0 ? noop : _ref$onChange,
    _ref$onHeightChange = _ref.onHeightChange,
    onHeightChange = _ref$onHeightChange === void 0 ? noop : _ref$onHeightChange,
    props = (0,_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref, _excluded);
  if (props.style) {
    if ('maxHeight' in props.style) {
      throw new Error('Using `style.maxHeight` for <TextareaAutosize/> is not supported. Please use `maxRows`.');
    }
    if ('minHeight' in props.style) {
      throw new Error('Using `style.minHeight` for <TextareaAutosize/> is not supported. Please use `minRows`.');
    }
  }
  var isControlled = props.value !== undefined;
  var libRef = react__WEBPACK_IMPORTED_MODULE_2__.useRef(null);
  var ref = (0,use_composed_ref__WEBPACK_IMPORTED_MODULE_4__["default"])(libRef, userRef);
  var heightRef = react__WEBPACK_IMPORTED_MODULE_2__.useRef(0);
  var measurementsCacheRef = react__WEBPACK_IMPORTED_MODULE_2__.useRef();
  var resizeTextarea = function resizeTextarea() {
    var node = libRef.current;
    var nodeSizingData = cacheMeasurements && measurementsCacheRef.current ? measurementsCacheRef.current : getSizingData$1(node);
    if (!nodeSizingData) {
      return;
    }
    measurementsCacheRef.current = nodeSizingData;
    var _calculateNodeHeight = calculateNodeHeight(nodeSizingData, node.value || node.placeholder || 'x', minRows, maxRows),
      height = _calculateNodeHeight[0],
      rowHeight = _calculateNodeHeight[1];
    if (heightRef.current !== height) {
      heightRef.current = height;
      node.style.setProperty('height', height + "px", 'important');
      onHeightChange(height, {
        rowHeight: rowHeight
      });
    }
  };
  var handleChange = function handleChange(event) {
    if (!isControlled) {
      resizeTextarea();
    }
    onChange(event);
  };
  {
    react__WEBPACK_IMPORTED_MODULE_2__.useLayoutEffect(resizeTextarea);
    useWindowResizeListener(resizeTextarea);
    useFontsLoadedListener(resizeTextarea);
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2__.createElement("textarea", (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, props, {
      onChange: handleChange,
      ref: ref
    }));
  }
};
var index = /* #__PURE__ */react__WEBPACK_IMPORTED_MODULE_2__.forwardRef(TextareaAutosize);




/***/ }),

/***/ "./node_modules/use-composed-ref/dist/use-composed-ref.esm.js":
/*!********************************************************************!*\
  !*** ./node_modules/use-composed-ref/dist/use-composed-ref.esm.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


var updateRef = function updateRef(ref, value) {
  if (typeof ref === 'function') {
    ref(value);
    return;
  }
  ref.current = value;
};

var useComposedRef = function useComposedRef(libRef, userRef) {
  var prevUserRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(function (instance) {
    libRef.current = instance;

    if (prevUserRef.current) {
      updateRef(prevUserRef.current, null);
    }

    prevUserRef.current = userRef;

    if (!userRef) {
      return;
    }

    updateRef(userRef, instance);
  }, [userRef]);
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (useComposedRef);


/***/ }),

/***/ "./node_modules/use-isomorphic-layout-effect/dist/use-isomorphic-layout-effect.browser.esm.js":
/*!****************************************************************************************************!*\
  !*** ./node_modules/use-isomorphic-layout-effect/dist/use-isomorphic-layout-effect.browser.esm.js ***!
  \****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


var index =  react__WEBPACK_IMPORTED_MODULE_0__.useLayoutEffect ;

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (index);


/***/ }),

/***/ "./node_modules/use-latest/dist/use-latest.esm.js":
/*!********************************************************!*\
  !*** ./node_modules/use-latest/dist/use-latest.esm.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ useLatest)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var use_isomorphic_layout_effect__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! use-isomorphic-layout-effect */ "./node_modules/use-isomorphic-layout-effect/dist/use-isomorphic-layout-effect.browser.esm.js");



var useLatest = function useLatest(value) {
  var ref = react__WEBPACK_IMPORTED_MODULE_0__.useRef(value);
  (0,use_isomorphic_layout_effect__WEBPACK_IMPORTED_MODULE_1__["default"])(function () {
    ref.current = value;
  });
  return ref;
};




/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = React;

/***/ }),

/***/ "react-dom":
/*!***************************!*\
  !*** external "ReactDOM" ***!
  \***************************/
/***/ ((module) => {

module.exports = ReactDOM;

/***/ }),

/***/ "./node_modules/markdown-to-jsx/dist/index.modern.js":
/*!***********************************************************!*\
  !*** ./node_modules/markdown-to-jsx/dist/index.modern.js ***!
  \***********************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   RuleType: () => (/* binding */ r),
/* harmony export */   compiler: () => (/* binding */ Ze),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   sanitizer: () => (/* binding */ Ue),
/* harmony export */   slugify: () => (/* binding */ Ce)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
function t(){return t=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e},t.apply(this,arguments)}const n=["children","options"],r={blockQuote:"0",breakLine:"1",breakThematic:"2",codeBlock:"3",codeFenced:"4",codeInline:"5",footnote:"6",footnoteReference:"7",gfmTask:"8",heading:"9",headingSetext:"10",htmlBlock:"11",htmlComment:"12",htmlSelfClosing:"13",image:"14",link:"15",linkAngleBraceStyleDetector:"16",linkBareUrlDetector:"17",linkMailtoDetector:"18",newlineCoalescer:"19",orderedList:"20",paragraph:"21",ref:"22",refImage:"23",refLink:"24",table:"25",tableSeparator:"26",text:"27",textBolded:"28",textEmphasized:"29",textEscaped:"30",textMarked:"31",textStrikethroughed:"32",unorderedList:"33"};var i;!function(e){e[e.MAX=0]="MAX",e[e.HIGH=1]="HIGH",e[e.MED=2]="MED",e[e.LOW=3]="LOW",e[e.MIN=4]="MIN"}(i||(i={}));const l=["allowFullScreen","allowTransparency","autoComplete","autoFocus","autoPlay","cellPadding","cellSpacing","charSet","className","classId","colSpan","contentEditable","contextMenu","crossOrigin","encType","formAction","formEncType","formMethod","formNoValidate","formTarget","frameBorder","hrefLang","inputMode","keyParams","keyType","marginHeight","marginWidth","maxLength","mediaGroup","minLength","noValidate","radioGroup","readOnly","rowSpan","spellCheck","srcDoc","srcLang","srcSet","tabIndex","useMap"].reduce((e,t)=>(e[t.toLowerCase()]=t,e),{for:"htmlFor"}),a={amp:"&",apos:"'",gt:">",lt:"<",nbsp:" ",quot:"“"},o=["style","script"],c=/([-A-Z0-9_:]+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|(?:\{((?:\\.|{[^}]*?}|[^}])*)\})))?/gi,s=/mailto:/i,d=/\n{2,}$/,u=/^(\s*>[\s\S]*?)(?=\n{2,})/,p=/^ *> ?/gm,f=/^ {2,}\n/,h=/^(?:( *[-*_])){3,} *(?:\n *)+\n/,m=/^\s*(`{3,}|~{3,}) *(\S+)?([^\n]*?)?\n([\s\S]+?)\s*\1 *(?:\n *)*\n?/,g=/^(?: {4}[^\n]+\n*)+(?:\n *)+\n?/,y=/^(`+)\s*([\s\S]*?[^`])\s*\1(?!`)/,k=/^(?:\n *)*\n/,x=/\r\n?/g,b=/^\[\^([^\]]+)](:(.*)((\n+ {4,}.*)|(\n(?!\[\^).+))*)/,v=/^\[\^([^\]]+)]/,S=/\f/g,E=/^---[ \t]*\n(.|\n)*\n---[ \t]*\n/,$=/^\s*?\[(x|\s)\]/,w=/^ *(#{1,6}) *([^\n]+?)(?: +#*)?(?:\n *)*(?:\n|$)/,C=/^ *(#{1,6}) +([^\n]+?)(?: +#*)?(?:\n *)*(?:\n|$)/,z=/^([^\n]+)\n *(=|-){3,} *(?:\n *)+\n/,L=/^ *(?!<[a-z][^ >/]* ?\/>)<([a-z][^ >/]*) ?((?:[^>]*[^/])?)>\n?(\s*(?:<\1[^>]*?>[\s\S]*?<\/\1>|(?!<\1\b)[\s\S])*?)<\/\1>(?!<\/\1>)\n*/i,A=/&([a-z0-9]+|#[0-9]{1,6}|#x[0-9a-fA-F]{1,6});/gi,T=/^<!--[\s\S]*?(?:-->)/,O=/^(data|aria|x)-[a-z_][a-z\d_.-]*$/,B=/^ *<([a-z][a-z0-9:]*)(?:\s+((?:<.*?>|[^>])*))?\/?>(?!<\/\1>)(\s*\n)?/i,M=/^\{.*\}$/,R=/^(https?:\/\/[^\s<]+[^<.,:;"')\]\s])/,I=/^<([^ >]+@[^ >]+)>/,U=/^<([^ >]+:\/[^ >]+)>/,D=/-([a-z])?/gi,j=/^(.*\|.*)\n(?: *(\|? *[-:]+ *\|[-| :]*)\n((?:.*\|.*\n)*))?\n?/,N=/^\[([^\]]*)\]:\s+<?([^\s>]+)>?\s*("([^"]*)")?/,H=/^!\[([^\]]*)\] ?\[([^\]]*)\]/,F=/^\[([^\]]*)\] ?\[([^\]]*)\]/,P=/(\[|\])/g,_=/(\n|^[-*]\s|^#|^ {2,}|^-{2,}|^>\s)/,W=/\t/g,G=/(^ *\||\| *$)/g,Z=/^ *:-+: *$/,q=/^ *:-+ *$/,Q=/^ *-+: *$/,V="((?:\\[.*?\\][([].*?[)\\]]|<.*?>(?:.*?<.*?>)?|`.*?`|~~.*?~~|==.*?==|.|\\n)*?)",X=new RegExp(`^([*_])\\1${V}\\1\\1(?!\\1)`),J=new RegExp(`^([*_])${V}\\1(?!\\1|\\w)`),K=new RegExp(`^==${V}==`),Y=new RegExp(`^~~${V}~~`),ee=/^\\([^0-9A-Za-z\s])/,te=/^[\s\S]+?(?=[^0-9A-Z\s\u00c0-\uffff&#;.()'"]|\d+\.|\n\n| {2,}\n|\w+:\S|$)/i,ne=/^\n+/,re=/^([ \t]*)/,ie=/\\([^\\])/g,le=/ *\n+$/,ae=/(?:^|\n)( *)$/,oe="(?:\\d+\\.)",ce="(?:[*+-])";function se(e){return"( *)("+(1===e?oe:ce)+") +"}const de=se(1),ue=se(2);function pe(e){return new RegExp("^"+(1===e?de:ue))}const fe=pe(1),he=pe(2);function me(e){return new RegExp("^"+(1===e?de:ue)+"[^\\n]*(?:\\n(?!\\1"+(1===e?oe:ce)+" )[^\\n]*)*(\\n|$)","gm")}const ge=me(1),ye=me(2);function ke(e){const t=1===e?oe:ce;return new RegExp("^( *)("+t+") [\\s\\S]+?(?:\\n{2,}(?! )(?!\\1"+t+" (?!"+t+" ))\\n*|\\s*\\n*$)")}const xe=ke(1),be=ke(2);function ve(e,t){const n=1===t,i=n?xe:be,l=n?ge:ye,a=n?fe:he;return{match(e,t,n){const r=ae.exec(n);return r&&(t.list||!t.inline&&!t.simple)?i.exec(e=r[1]+e):null},order:1,parse(e,t,r){const i=n?+e[2]:void 0,o=e[0].replace(d,"\n").match(l);let c=!1;return{items:o.map(function(e,n){const i=a.exec(e)[0].length,l=new RegExp("^ {1,"+i+"}","gm"),s=e.replace(l,"").replace(a,""),d=n===o.length-1,u=-1!==s.indexOf("\n\n")||d&&c;c=u;const p=r.inline,f=r.list;let h;r.list=!0,u?(r.inline=!1,h=s.replace(le,"\n\n")):(r.inline=!0,h=s.replace(le,""));const m=t(h,r);return r.inline=p,r.list=f,m}),ordered:n,start:i}},render:(t,n,i)=>e(t.ordered?"ol":"ul",{key:i.key,start:t.type===r.orderedList?t.start:void 0},t.items.map(function(t,r){return e("li",{key:r},n(t,i))}))}}const Se=new RegExp("^\\[((?:\\[[^\\]]*\\]|[^\\[\\]]|\\](?=[^\\[]*\\]))*)\\]\\(\\s*<?((?:\\([^)]*\\)|[^\\s\\\\]|\\\\.)*?)>?(?:\\s+['\"]([\\s\\S]*?)['\"])?\\s*\\)"),Ee=/^!\[(.*?)\]\( *((?:\([^)]*\)|[^() ])*) *"?([^)"]*)?"?\)/,$e=[u,m,g,w,z,C,T,j,ge,xe,ye,be],we=[...$e,/^[^\n]+(?:  \n|\n{2,})/,L,B];function Ce(e){return e.replace(/[ÀÁÂÃÄÅàáâãäåæÆ]/g,"a").replace(/[çÇ]/g,"c").replace(/[ðÐ]/g,"d").replace(/[ÈÉÊËéèêë]/g,"e").replace(/[ÏïÎîÍíÌì]/g,"i").replace(/[Ññ]/g,"n").replace(/[øØœŒÕõÔôÓóÒò]/g,"o").replace(/[ÜüÛûÚúÙù]/g,"u").replace(/[ŸÿÝý]/g,"y").replace(/[^a-z0-9- ]/gi,"").replace(/ /gi,"-").toLowerCase()}function ze(e){return Q.test(e)?"right":Z.test(e)?"center":q.test(e)?"left":null}function Le(e,t,n,i){const l=n.inTable;n.inTable=!0;let a=e.trim().split(/( *(?:`[^`]*`|<.*?>.*?<\/.*?>(?!<\/.*?>)|\\\||\|) *)/).reduce((e,l)=>("|"===l.trim()?e.push(i?{type:r.tableSeparator}:{type:r.text,text:l}):""!==l&&e.push.apply(e,t(l,n)),e),[]);n.inTable=l;let o=[[]];return a.forEach(function(e,t){e.type===r.tableSeparator?0!==t&&t!==a.length-1&&o.push([]):(e.type!==r.text||null!=a[t+1]&&a[t+1].type!==r.tableSeparator||(e.text=e.text.trimEnd()),o[o.length-1].push(e))}),o}function Ae(e,t,n){n.inline=!0;const i=e[2]?e[2].replace(G,"").split("|").map(ze):[],l=e[3]?function(e,t,n){return e.trim().split("\n").map(function(e){return Le(e,t,n,!0)})}(e[3],t,n):[],a=Le(e[1],t,n,!!l.length);return n.inline=!1,l.length?{align:i,cells:l,header:a,type:r.table}:{children:a,type:r.paragraph}}function Te(e,t){return null==e.align[t]?{}:{textAlign:e.align[t]}}function Oe(e){return function(t,n){return n.inline?e.exec(t):null}}function Be(e){return function(t,n){return n.inline||n.simple?e.exec(t):null}}function Me(e){return function(t,n){return n.inline||n.simple?null:e.exec(t)}}function Re(e){return function(t){return e.exec(t)}}function Ie(e,t,n){if(t.inline||t.simple)return null;if(n&&!n.endsWith("\n"))return null;let r="";e.split("\n").every(e=>!$e.some(t=>t.test(e))&&(r+=e+"\n",e.trim()));const i=r.trimEnd();return""==i?null:[r,i]}function Ue(e){try{if(decodeURIComponent(e).replace(/[^A-Za-z0-9/:]/g,"").match(/^\s*(javascript|vbscript|data(?!:image)):/i))return null}catch(e){return null}return e}function De(e){return e.replace(ie,"$1")}function je(e,t,n){const r=n.inline||!1,i=n.simple||!1;n.inline=!0,n.simple=!0;const l=e(t,n);return n.inline=r,n.simple=i,l}function Ne(e,t,n){const r=n.inline||!1,i=n.simple||!1;n.inline=!1,n.simple=!0;const l=e(t,n);return n.inline=r,n.simple=i,l}function He(e,t,n){const r=n.inline||!1;n.inline=!1;const i=e(t,n);return n.inline=r,i}const Fe=(e,t,n)=>({children:je(t,e[1],n)});function Pe(){return{}}function _e(){return null}function We(...e){return e.filter(Boolean).join(" ")}function Ge(e,t,n){let r=e;const i=t.split(".");for(;i.length&&(r=r[i[0]],void 0!==r);)i.shift();return r||n}function Ze(n="",i={}){function d(e,n,...r){const l=Ge(i.overrides,`${e}.props`,{});return i.createElement(function(e,t){const n=Ge(t,e);return n?"function"==typeof n||"object"==typeof n&&"render"in n?n:Ge(t,`${e}.component`,e):e}(e,i.overrides),t({},n,l,{className:We(null==n?void 0:n.className,l.className)||void 0}),...r)}function G(t){t=t.replace(E,"");let n=!1;i.forceInline?n=!0:i.forceBlock||(n=!1===_.test(t));const r=le(ie(n?t:`${t.trimEnd().replace(ne,"")}\n\n`,{inline:n}));for(;"string"==typeof r[r.length-1]&&!r[r.length-1].trim();)r.pop();if(null===i.wrapper)return r;const l=i.wrapper||(n?"span":"div");let a;if(r.length>1||i.forceWrapper)a=r;else{if(1===r.length)return a=r[0],"string"==typeof a?d("span",{key:"outer"},a):a;a=null}return react__WEBPACK_IMPORTED_MODULE_0__.createElement(l,{key:"outer"},a)}function Z(t,n){const r=n.match(c);return r?r.reduce(function(n,r,a){const o=r.indexOf("=");if(-1!==o){const c=function(e){return-1!==e.indexOf("-")&&null===e.match(O)&&(e=e.replace(D,function(e,t){return t.toUpperCase()})),e}(r.slice(0,o)).trim(),s=function(e){const t=e[0];return('"'===t||"'"===t)&&e.length>=2&&e[e.length-1]===t?e.slice(1,-1):e}(r.slice(o+1).trim()),d=l[c]||c,u=n[d]=function(e,t,n,r){return"style"===t?n.split(/;\s?/).reduce(function(e,t){const n=t.slice(0,t.indexOf(":"));return e[n.trim().replace(/(-[a-z])/g,e=>e[1].toUpperCase())]=t.slice(n.length+1).trim(),e},{}):"href"===t||"src"===t?r(n,e,t):(n.match(M)&&(n=n.slice(1,n.length-1)),"true"===n||"false"!==n&&n)}(t,c,s,i.sanitizer);"string"==typeof u&&(L.test(u)||B.test(u))&&(n[d]=react__WEBPACK_IMPORTED_MODULE_0__.cloneElement(G(u.trim()),{key:a}))}else"style"!==r&&(n[l[r]||r]=!0);return n},{}):null}i.overrides=i.overrides||{},i.sanitizer=i.sanitizer||Ue,i.slugify=i.slugify||Ce,i.namedCodesToUnicode=i.namedCodesToUnicode?t({},a,i.namedCodesToUnicode):a,i.createElement=i.createElement||react__WEBPACK_IMPORTED_MODULE_0__.createElement;const q=[],Q={},V={[r.blockQuote]:{match:Me(u),order:1,parse:(e,t,n)=>({children:t(e[0].replace(p,""),n)}),render:(e,t,n)=>d("blockquote",{key:n.key},t(e.children,n))},[r.breakLine]:{match:Re(f),order:1,parse:Pe,render:(e,t,n)=>d("br",{key:n.key})},[r.breakThematic]:{match:Me(h),order:1,parse:Pe,render:(e,t,n)=>d("hr",{key:n.key})},[r.codeBlock]:{match:Me(g),order:0,parse:e=>({lang:void 0,text:e[0].replace(/^ {4}/gm,"").replace(/\n+$/,"")}),render:(e,n,r)=>d("pre",{key:r.key},d("code",t({},e.attrs,{className:e.lang?`lang-${e.lang}`:""}),e.text))},[r.codeFenced]:{match:Me(m),order:0,parse:e=>({attrs:Z("code",e[3]||""),lang:e[2]||void 0,text:e[4],type:r.codeBlock})},[r.codeInline]:{match:Be(y),order:3,parse:e=>({text:e[2]}),render:(e,t,n)=>d("code",{key:n.key},e.text)},[r.footnote]:{match:Me(b),order:0,parse:e=>(q.push({footnote:e[2],identifier:e[1]}),{}),render:_e},[r.footnoteReference]:{match:Oe(v),order:1,parse:e=>({target:`#${i.slugify(e[1],Ce)}`,text:e[1]}),render:(e,t,n)=>d("a",{key:n.key,href:i.sanitizer(e.target,"a","href")},d("sup",{key:n.key},e.text))},[r.gfmTask]:{match:Oe($),order:1,parse:e=>({completed:"x"===e[1].toLowerCase()}),render:(e,t,n)=>d("input",{checked:e.completed,key:n.key,readOnly:!0,type:"checkbox"})},[r.heading]:{match:Me(i.enforceAtxHeadings?C:w),order:1,parse:(e,t,n)=>({children:je(t,e[2],n),id:i.slugify(e[2],Ce),level:e[1].length}),render:(e,t,n)=>d(`h${e.level}`,{id:e.id,key:n.key},t(e.children,n))},[r.headingSetext]:{match:Me(z),order:0,parse:(e,t,n)=>({children:je(t,e[1],n),level:"="===e[2]?1:2,type:r.heading})},[r.htmlBlock]:{match:Re(L),order:1,parse(e,t,n){const[,r]=e[3].match(re),i=new RegExp(`^${r}`,"gm"),l=e[3].replace(i,""),a=(c=l,we.some(e=>e.test(c))?He:je);var c;const s=e[1].toLowerCase(),d=-1!==o.indexOf(s),u=(d?s:e[1]).trim(),p={attrs:Z(u,e[2]),noInnerParse:d,tag:u};return n.inAnchor=n.inAnchor||"a"===s,d?p.text=e[3]:p.children=a(t,l,n),n.inAnchor=!1,p},render:(e,n,r)=>d(e.tag,t({key:r.key},e.attrs),e.text||n(e.children,r))},[r.htmlSelfClosing]:{match:Re(B),order:1,parse(e){const t=e[1].trim();return{attrs:Z(t,e[2]||""),tag:t}},render:(e,n,r)=>d(e.tag,t({},e.attrs,{key:r.key}))},[r.htmlComment]:{match:Re(T),order:1,parse:()=>({}),render:_e},[r.image]:{match:Be(Ee),order:1,parse:e=>({alt:e[1],target:De(e[2]),title:e[3]}),render:(e,t,n)=>d("img",{key:n.key,alt:e.alt||void 0,title:e.title||void 0,src:i.sanitizer(e.target,"img","src")})},[r.link]:{match:Oe(Se),order:3,parse:(e,t,n)=>({children:Ne(t,e[1],n),target:De(e[2]),title:e[3]}),render:(e,t,n)=>d("a",{key:n.key,href:i.sanitizer(e.target,"a","href"),title:e.title},t(e.children,n))},[r.linkAngleBraceStyleDetector]:{match:Oe(U),order:0,parse:e=>({children:[{text:e[1],type:r.text}],target:e[1],type:r.link})},[r.linkBareUrlDetector]:{match:(e,t)=>t.inAnchor?null:Oe(R)(e,t),order:0,parse:e=>({children:[{text:e[1],type:r.text}],target:e[1],title:void 0,type:r.link})},[r.linkMailtoDetector]:{match:Oe(I),order:0,parse(e){let t=e[1],n=e[1];return s.test(n)||(n="mailto:"+n),{children:[{text:t.replace("mailto:",""),type:r.text}],target:n,type:r.link}}},[r.orderedList]:ve(d,1),[r.unorderedList]:ve(d,2),[r.newlineCoalescer]:{match:Me(k),order:3,parse:Pe,render:()=>"\n"},[r.paragraph]:{match:Ie,order:3,parse:Fe,render:(e,t,n)=>d("p",{key:n.key},t(e.children,n))},[r.ref]:{match:Oe(N),order:0,parse:e=>(Q[e[1]]={target:e[2],title:e[4]},{}),render:_e},[r.refImage]:{match:Be(H),order:0,parse:e=>({alt:e[1]||void 0,ref:e[2]}),render:(e,t,n)=>Q[e.ref]?d("img",{key:n.key,alt:e.alt,src:i.sanitizer(Q[e.ref].target,"img","src"),title:Q[e.ref].title}):null},[r.refLink]:{match:Oe(F),order:0,parse:(e,t,n)=>({children:t(e[1],n),fallbackChildren:t(e[0].replace(P,"\\$1"),n),ref:e[2]}),render:(e,t,n)=>Q[e.ref]?d("a",{key:n.key,href:i.sanitizer(Q[e.ref].target,"a","href"),title:Q[e.ref].title},t(e.children,n)):d("span",{key:n.key},t(e.fallbackChildren,n))},[r.table]:{match:Me(j),order:1,parse:Ae,render(e,t,n){const r=e;return d("table",{key:n.key},d("thead",null,d("tr",null,r.header.map(function(e,i){return d("th",{key:i,style:Te(r,i)},t(e,n))}))),d("tbody",null,r.cells.map(function(e,i){return d("tr",{key:i},e.map(function(e,i){return d("td",{key:i,style:Te(r,i)},t(e,n))}))})))}},[r.text]:{match:Re(te),order:4,parse:e=>({text:e[0].replace(A,(e,t)=>i.namedCodesToUnicode[t]?i.namedCodesToUnicode[t]:e)}),render:e=>e.text},[r.textBolded]:{match:Be(X),order:2,parse:(e,t,n)=>({children:t(e[2],n)}),render:(e,t,n)=>d("strong",{key:n.key},t(e.children,n))},[r.textEmphasized]:{match:Be(J),order:3,parse:(e,t,n)=>({children:t(e[2],n)}),render:(e,t,n)=>d("em",{key:n.key},t(e.children,n))},[r.textEscaped]:{match:Be(ee),order:1,parse:e=>({text:e[1],type:r.text})},[r.textMarked]:{match:Be(K),order:3,parse:Fe,render:(e,t,n)=>d("mark",{key:n.key},t(e.children,n))},[r.textStrikethroughed]:{match:Be(Y),order:3,parse:Fe,render:(e,t,n)=>d("del",{key:n.key},t(e.children,n))}};!0===i.disableParsingRawHTML&&(delete V[r.htmlBlock],delete V[r.htmlSelfClosing]);const ie=function(e){let t=Object.keys(e);function n(r,i){let l=[],a="";for(;r;){let o=0;for(;o<t.length;){const c=t[o],s=e[c],d=s.match(r,i,a);if(d){const e=d[0];r=r.substring(e.length);const t=s.parse(d,n,i);null==t.type&&(t.type=c),l.push(t),a=e;break}o++}}return l}return t.sort(function(t,n){let r=e[t].order,i=e[n].order;return r!==i?r-i:t<n?-1:1}),function(e,t){return n(function(e){return e.replace(x,"\n").replace(S,"").replace(W,"    ")}(e),t)}}(V),le=(ae=function(e,t){return function(n,r,i){const l=e[n.type].render;return t?t(()=>l(n,r,i),n,r,i):l(n,r,i)}}(V,i.renderRule),function e(t,n={}){if(Array.isArray(t)){const r=n.key,i=[];let l=!1;for(let r=0;r<t.length;r++){n.key=r;const a=e(t[r],n),o="string"==typeof a;o&&l?i[i.length-1]+=a:null!==a&&i.push(a),l=o}return n.key=r,i}return ae(t,e,n)});var ae;const oe=G(n);return q.length?d("div",null,oe,d("footer",{key:"footer"},q.map(function(e){return d("div",{id:i.slugify(e.identifier,Ce),key:e.identifier},e.identifier,le(ie(e.footnote,{inline:!0})))}))):oe}/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (t=>{let{children:r="",options:i}=t,l=function(e,t){if(null==e)return{};var n,r,i={},l=Object.keys(e);for(r=0;r<l.length;r++)t.indexOf(n=l[r])>=0||(i[n]=e[n]);return i}(t,n);return react__WEBPACK_IMPORTED_MODULE_0__.cloneElement(Ze(r,i),l)});
//# sourceMappingURL=index.modern.js.map


/***/ }),

/***/ "./node_modules/react-textarea-autosize/node_modules/@babel/runtime/helpers/esm/extends.js":
/*!*************************************************************************************************!*\
  !*** ./node_modules/react-textarea-autosize/node_modules/@babel/runtime/helpers/esm/extends.js ***!
  \*************************************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _extends)
/* harmony export */ });
function _extends() {
  _extends = Object.assign ? Object.assign.bind() : function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  return _extends.apply(this, arguments);
}

/***/ }),

/***/ "./node_modules/react-textarea-autosize/node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js":
/*!**********************************************************************************************************************!*\
  !*** ./node_modules/react-textarea-autosize/node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js ***!
  \**********************************************************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _objectWithoutPropertiesLoose)
/* harmony export */ });
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;
  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }
  return target;
}

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
/*!***************************!*\
  !*** ./app/js/chatbot.js ***!
  \***************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _app_chatbot_ChatbotSystem__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @app/chatbot/ChatbotSystem */ "./app/js/chatbot/ChatbotSystem.js");
/* harmony import */ var _app_chatbot_DiscussionsSystem__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @app/chatbot/DiscussionsSystem */ "./app/js/chatbot/DiscussionsSystem.js");
const {
  render
} = wp.element;


function decodeHtmlEntities(encodedStr) {
  const textarea = document.createElement('textarea');
  textarea.innerHTML = encodedStr;
  return textarea.value;
}

// Main initialization function
function initializeMwai() {
  function processContainers(containers, component) {
    containers.forEach(container => {
      // Skip if already initialized
      if (container.hasAttribute('data-mwai-initialized')) {
        return;
      }
      const paramsAttr = container.getAttribute('data-params');
      const systemAttr = container.getAttribute('data-system');
      const themeAttr = container.getAttribute('data-theme');

      // Check if attributes exist before parsing
      if (!paramsAttr || !systemAttr || !themeAttr) {
        console.warn('MWAI: Missing required attributes for initialization', container);
        return;
      }
      const params = JSON.parse(decodeHtmlEntities(paramsAttr));
      const system = JSON.parse(decodeHtmlEntities(systemAttr));
      const theme = JSON.parse(decodeHtmlEntities(themeAttr));

      // Mark as initialized before removing attributes
      container.setAttribute('data-mwai-initialized', 'true');
      container.removeAttribute('data-params');
      container.removeAttribute('data-system');
      container.removeAttribute('data-theme');
      render(component({
        system,
        params,
        theme
      }), container);
    });
  }
  const chatbotContainers = document.querySelectorAll('.mwai-chatbot-container');
  processContainers(chatbotContainers, _app_chatbot_ChatbotSystem__WEBPACK_IMPORTED_MODULE_0__["default"]);
  const discussionsContainers = document.querySelectorAll('.mwai-discussions-container');
  processContainers(discussionsContainers, _app_chatbot_DiscussionsSystem__WEBPACK_IMPORTED_MODULE_1__["default"]);
}
document.addEventListener('DOMContentLoaded', initializeMwai);

// If the user wants to initialize manually, well, he can do it.
window.mwaiInitialize = initializeMwai;
/******/ })()
;
//# sourceMappingURL=chatbot.js.map