/**
 * Bundled by jsDelivr using Rollup v2.79.2 and Terser v5.37.0.
 * Original file: /npm/desandro-matches-selector@2.0.2/matches-selector.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
var e,t={exports:{}};e=t,function(t,r){e.exports?e.exports=r():t.matchesSelector=r()}(window,(function(){var e=function(){var e=window.Element.prototype;if(e.matches)return"matches";if(e.matchesSelector)return"matchesSelector";for(var t=["webkit","moz","ms","o"],r=0;r<t.length;r++){var o=t[r]+"MatchesSelector";if(e[o])return o}}();return function(t,r){return t[e](r)}}));var r=t.exports;export{r as default};
