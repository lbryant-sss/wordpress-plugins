var FixedColumns;!function(v,h){(FixedColumns=function(t,e){!this instanceof FixedColumns?alert("FixedColumns warning: FixedColumns must be initialised with the 'new' keyword."):(void 0===e&&(e={}),this.s={dt:t.fnSettings(),iTableColumns:t.fnSettings().aoColumns.length,aiWidths:[],bOldIE:v.support.msie&&("6.0"==v.support.version||"7.0"==v.support.version)},this.dom={scroller:null,header:null,body:null,footer:null,grid:{wrapper:null,dt:null,left:{wrapper:null,head:null,body:null,foot:null},right:{wrapper:null,head:null,body:null,foot:null}},clone:{left:{header:null,body:null,footer:null},right:{header:null,body:null,footer:null}}},(this.s.dt.oFixedColumns=this)._fnConstruct(e))}).prototype={fnUpdate:function(){this._fnDraw(!0)},fnRedrawLayout:function(){this._fnGridLayout()},fnRecalculateHeight:function(t){t._DTTC_iHeight=null,t.style.height="auto"},fnSetRowHeight:function(t,e){var i=v(t).children(":first"),i=i.outerHeight()-i.height();v.support.mozilla||v.support.opera?t.style.height=e+"px":v(t).children().height(e-i)},_fnConstruct:function(t){var e,i,s=this;if("function"!=typeof this.s.dt.oInstance.fnVersionCheck||!0!==this.s.dt.oInstance.fnVersionCheck("1.8.0"))alert("FixedColumns "+FixedColumns.VERSION+" required DataTables 1.8.0 or later. Please upgrade your DataTables installation");else if(""===this.s.dt.oScroll.sX)this.s.dt.oInstance.oApi._fnLog(this.s.dt,1,"FixedColumns is not needed (no x-scrolling in DataTables enabled), so no action will be taken. Use 'FixedHeader' for column fixing when scrolling is not enabled");else{this.s=v.extend(!0,this.s,FixedColumns.defaults,t),this.dom.grid.dt=v(this.s.dt.nTable).parents("div.dataTables_scroll")[0],this.dom.scroller=v("div.dataTables_scrollBody",this.dom.grid.dt)[0];var t=v(this.dom.grid.dt).width(),o=0,d=0;for(v("tbody>tr:eq(0)>td",this.s.dt.nTable).each(function(t){i=v(this).outerWidth(),s.s.aiWidths.push(i),t<s.s.iLeftColumns&&(o+=i),s.s.iTableColumns-s.s.iRightColumns<=t&&(d+=i)}),null===this.s.iLeftWidth&&(this.s.iLeftWidth="fixed"==this.s.sLeftWidth?o:o/t*100),null===this.s.iRightWidth&&(this.s.iRightWidth="fixed"==this.s.sRightWidth?d:d/t*100),this._fnGridSetup(),e=0;e<this.s.iLeftColumns;e++)this.s.dt.oInstance.fnSetColumnVis(e,!1);for(e=this.s.iTableColumns-this.s.iRightColumns;e<this.s.iTableColumns;e++)this.s.dt.oInstance.fnSetColumnVis(e,!1);v(this.dom.scroller).scroll(function(){s.dom.grid.left.body.scrollTop=s.dom.scroller.scrollTop,0<s.s.iRightColumns&&(s.dom.grid.right.body.scrollTop=s.dom.scroller.scrollTop)}),v(h).resize(function(){s._fnGridLayout.call(s)});var l=!0;this.s.dt.aoDrawCallback=[{fn:function(){s._fnDraw.call(s,l),s._fnGridHeight(s),l=!1},sName:"FixedColumns"}].concat(this.s.dt.aoDrawCallback),this._fnGridLayout(),this._fnGridHeight(),this.s.dt.oInstance.fnDraw(!1)}},_fnGridSetup:function(){this.dom.body=this.s.dt.nTable,this.dom.header=this.s.dt.nTHead.parentNode,this.dom.header.parentNode.parentNode.style.position="relative";var t=v('<div class="DTFC_ScrollWrapper" style="position:relative; clear:both;"><div class="DTFC_LeftWrapper" style="position:absolute; top:0; left:0;"><div class="DTFC_LeftHeadWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div><div class="DTFC_LeftBodyWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div><div class="DTFC_LeftFootWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div></div><div class="DTFC_RightWrapper" style="position:absolute; top:0; left:0;"><div class="DTFC_RightHeadWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div><div class="DTFC_RightBodyWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div><div class="DTFC_RightFootWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div></div></div>')[0];nLeft=t.childNodes[0],nRight=t.childNodes[1],this.dom.grid.wrapper=t,this.dom.grid.left.wrapper=nLeft,this.dom.grid.left.head=nLeft.childNodes[0],this.dom.grid.left.body=nLeft.childNodes[1],0<this.s.iRightColumns&&(this.dom.grid.right.wrapper=nRight,this.dom.grid.right.head=nRight.childNodes[0],this.dom.grid.right.body=nRight.childNodes[1]),this.s.dt.nTFoot&&(this.dom.footer=this.s.dt.nTFoot.parentNode,this.dom.grid.left.foot=nLeft.childNodes[2],0<this.s.iRightColumns&&(this.dom.grid.right.foot=nRight.childNodes[2])),t.appendChild(nLeft),this.dom.grid.dt.parentNode.insertBefore(t,this.dom.grid.dt),t.appendChild(this.dom.grid.dt),this.dom.grid.dt.style.position="absolute",this.dom.grid.dt.style.top="0px",this.dom.grid.dt.style.left=this.s.iLeftWidth+"px",this.dom.grid.dt.style.width=v(this.dom.grid.dt).width()-this.s.iLeftWidth-this.s.iRightWidth+"px"},_fnGridLayout:function(){var t=this.dom.grid,e=v(t.wrapper).width(),i=0,s=0,o=e-(i="fixed"==this.s.sLeftWidth?this.s.iLeftWidth:this.s.iLeftWidth/100*e)-(s="fixed"==this.s.sRightWidth?this.s.iRightWidth:this.s.iRightWidth/100*e);t.left.wrapper.style.width=i+"px",t.dt.style.width=o+"px",t.dt.style.left=i+"px",0<this.s.iRightColumns&&(t.right.wrapper.style.width=s+"px",t.right.wrapper.style.left=e-s+"px")},_fnGridHeight:function(){var t=this.dom.grid,e=v(this.dom.grid.dt).height();t.wrapper.style.height=e+"px",t.left.body.style.height=v(this.dom.scroller).height()+"px",t.left.wrapper.style.height=e+"px",0<this.s.iRightColumns&&(t.right.wrapper.style.height=e+"px",t.right.body.style.height=v(this.dom.scroller).height()+"px")},_fnDraw:function(t){this._fnCloneLeft(t),this._fnCloneRight(t),null!==this.s.fnDrawCallback&&this.s.fnDrawCallback.call(this,this.dom.clone.left,this.dom.clone.right),v(this).trigger("draw",{leftClone:this.dom.clone.left,rightClone:this.dom.clone.right})},_fnCloneRight:function(t){if(!(this.s.iRightColumns<=0)){for(var e=[],i=this.s.iTableColumns-this.s.iRightColumns;i<this.s.iTableColumns;i++)e.push(i);this._fnClone(this.dom.clone.right,this.dom.grid.right,e,t)}},_fnCloneLeft:function(t){if(!(this.s.iLeftColumns<=0)){for(var e=[],i=0;i<this.s.iLeftColumns;i++)e.push(i);this._fnClone(this.dom.clone.left,this.dom.grid.left,e,t)}},_fnCopyLayout:function(t,e){for(var i=[],s=[],o=[],d=0,l=t.length;d<l;d++){var h=[];h.nTr=v(t[d].nTr).clone(!0)[0];for(var n,r,a=0,f=this.s.iTableColumns;a<f;a++)-1!==v.inArray(a,e)&&(-1===(n=v.inArray(t[d][a].cell,o))?(r=v(t[d][a].cell).clone(!0)[0],s.push(r),o.push(t[d][a].cell),h.push({cell:r,unique:t[d][a].unique})):h.push({cell:s[n],unique:t[d][a].unique}));i.push(h)}return i},_fnClone:function(t,e,o,i){var s,d,l,h,n,r,a=this;if(i){null!==t.header&&t.header.parentNode.removeChild(t.header),t.header=v(this.dom.header).clone(!0)[0],t.header.className+=" DTFC_Cloned",t.header.style.width="100%",e.head.appendChild(t.header);var f=this._fnCopyLayout(this.s.dt.aoHeader,o),p=v(">thead",t.header);for(p.empty(),s=0,d=f.length;s<d;s++)p[0].appendChild(f[s].nTr);this.s.dt.oApi._fnDrawHead(this.s.dt,f,!0)}else{var f=this._fnCopyLayout(this.s.dt.aoHeader,o),u=[];for(this.s.dt.oApi._fnDetectHeader(u,v(">thead",t.header)[0]),s=0,d=f.length;s<d;s++)for(l=0,h=f[s].length;l<h;l++){u[s][l].cell.className=f[s][l].cell.className;var c=a.s.dt;v("span.DataTables_sort_icon",u[s][l].cell).each(function(){if(c.aoColumns[l].bSortable){var t=a.s.dt.aaSorting,e=a.s.dt.oClasses;if(c.bJUI){for(var i,s=v(this),o=(s.removeClass(e.sSortJUIAsc+" "+e.sSortJUIDesc+" "+e.sSortJUI+" "+e.sSortJUIAscAllowed+" "+e.sSortJUIDescAllowed),-1),d=0;d<t.length;d++)if(t[d][0]==l){"asc"==t[d][1]?e.sSortAsc:e.sSortDesc,o=d;break}i=-1==o?c.aoColumns[l].sSortingClassJUI:"asc"==t[o][1]?e.sSortJUIAsc:e.sSortJUIDesc,s.addClass(i)}}})}}this._fnEqualiseHeights("thead",this.dom.header,t.header),"auto"==this.s.sHeightMatch&&v(">tbody>tr",a.dom.body).css("height","auto"),null!==t.body&&(t.body.parentNode.removeChild(t.body),t.body=null),t.body=v(this.dom.body).clone(!0)[0],t.body.className+=" DTFC_Cloned",t.body.style.paddingBottom=this.s.dt.oScroll.iBarWidth+"px",t.body.style.marginBottom=2*this.s.dt.oScroll.iBarWidth+"px",null!==t.body.getAttribute("id")&&t.body.removeAttribute("id"),v(">thead>tr",t.body).empty(),v(">tfoot",t.body).remove();var g=v("tbody",t.body)[0];if(v(g).empty(),0<this.s.dt.aiDisplay.length){for(var m=v(">thead>tr",t.body)[0],y=0;y<o.length;y++)n=o[y],(r=this.s.dt.aoColumns[n].nTh).innerHTML="",(oStyle=r.style).paddingTop="0",oStyle.paddingBottom="0",oStyle.borderTopWidth="0",oStyle.borderBottomWidth="0",oStyle.height=0,oStyle.width="120px",m.appendChild(r);v(">tbody>tr",a.dom.body).each(function(t){var e=!1===a.s.dt.oFeatures.bServerSide?a.s.dt.aiDisplay[a.s.dt._iDisplayStart+t]:t,i=a.s.dt.aoData[e].anCells||v(this).children("td, th"),s=this.cloneNode(!1);for(s.removeAttribute("id"),s.setAttribute("data-dt-row",e),y=0;y<o.length;y++)n=o[y],0<i.length&&((r=v(i[n]).clone(!0,!0)[0]).setAttribute("data-dt-row",e),r.setAttribute("data-dt-column",y),s.appendChild(r));g.appendChild(s)})}else v(">tbody>tr",a.dom.body).each(function(t){(r=this.cloneNode(!0)).className+=" DTFC_NoData",v("td",r).html(""),g.appendChild(r)});if(t.body.style.width="100%",e.body.appendChild(t.body),this._fnEqualiseHeights("tbody",a.dom.body,t.body),null!==this.s.dt.nTFoot){if(i){null!==t.footer&&t.footer.parentNode.removeChild(t.footer),t.footer=v(this.dom.footer).clone(!0)[0],t.footer.className+=" DTFC_Cloned",t.footer.style.width="100%",e.foot.appendChild(t.footer);var f=this._fnCopyLayout(this.s.dt.aoFooter,o),C=v(">tfoot",t.footer);for(C.empty(),s=0,d=f.length;s<d;s++)C[0].appendChild(f[s].nTr);this.s.dt.oApi._fnDrawHead(this.s.dt,f,!0)}else{var f=this._fnCopyLayout(this.s.dt.aoFooter,o),b=[];for(this.s.dt.oApi._fnDetectHeader(b,v(">tfoot",t.footer)[0]),s=0,d=f.length;s<d;s++)for(l=0,h=f[s].length;l<h;l++)b[s][l].cell.className=f[s][l].cell.className}this._fnEqualiseHeights("tfoot",this.dom.footer,t.footer)}i=this.s.dt.oApi._fnGetUniqueThs(this.s.dt,v(">thead",t.header)[0]);v(i).each(function(t){n=o[t],this.style.width="120px"}),null!==a.s.dt.nTFoot&&(i=this.s.dt.oApi._fnGetUniqueThs(this.s.dt,v(">tfoot",t.footer)[0]),v(i).each(function(t){n=o[t],this.style.width="120px"}))},_fnGetTrNodes:function(t){for(var e=[],i=0,s=t.childNodes.length;i<s;i++)"TR"==t.childNodes[i].nodeName.toUpperCase()&&e.push(t.childNodes[i]);return e},_fnEqualiseHeights:function(t,e,i){if("none"!=this.s.sHeightMatch||"thead"===t||"tfoot"===t)for(var s,o,d=e.getElementsByTagName(t)[0],i=i.getElementsByTagName(t)[0],t=v(">"+t+">tr:eq(0)",e).children(":first"),l=t.outerHeight()-t.height(),h=this._fnGetTrNodes(d),n=this._fnGetTrNodes(i),r=0,a=n.length;r<a;r++)"semiauto"==this.s.sHeightMatch&&void 0!==h[r]._DTTC_iHeight&&null!==h[r]._DTTC_iHeight?v.support.msie&&v(n[r]).children().height(h[r]._DTTC_iHeight-l):(o=(s=h[r].offsetHeight)<(o=n[r].offsetHeight)?o:s,"semiauto"==this.s.sHeightMatch&&(h[r]._DTTC_iHeight=o),v.support.msie&&v.support.version<8?(v(n[r]).children().height(o-l),v(h[r]).children().height(o-l)):(n[r].style.height=o+"px",h[r].style.height=o+"px"))}},FixedColumns.defaults={iLeftColumns:1,iRightColumns:0,fnDrawCallback:null,sLeftWidth:"fixed",iLeftWidth:null,sRightWidth:"fixed",iRightWidth:null,sHeightMatch:"semiauto"},FixedColumns.prototype.CLASS="FixedColumns",FixedColumns.VERSION="2.0.3"}(jQuery,window,document);