var DymoPrinting = function (LabelPrinter) {

	var mainSelf = this;
	var SelectedPrinter;
	var splashImage = 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAA8AAD/4QODaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjAtYzA2MCA2MS4xMzQ3NzcsIDIwMTAvMDIvMTItMTc6MzI6MDAgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcFJpZ2h0cz0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3JpZ2h0cy8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bXBSaWdodHM6TWFya2VkPSJGYWxzZSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3NkRFRDZFODI4MEMxMUUxQjVBOUZGODI0MkJDNTg1OCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3NkRFRDZFNzI4MEMxMUUxQjVBOUZGODI0MkJDNTg1OCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgNy4wIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InV1aWQ6ZmMyMzc1OTgtYTEzZi0xMWRlLWJkZWItZTQ1MDFiZmNmNmQzIiBzdFJlZjpkb2N1bWVudElEPSJhZG9iZTpkb2NpZDpwaG90b3Nob3A6ZmExMGU2OWUtYTEzZC0xMWRlLWJkZWItZTQ1MDFiZmNmNmQzIi8+IDwvcmRmOkRlc2NyaXB0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';
	var ImageReelSelectLeft = 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAA8AAD/4QMpaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjAtYzA2MCA2MS4xMzQ3NzcsIDIwMTAvMDIvMTItMTc6MzI6MDAgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzUgV2luZG93cyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozQjk4NjhEMDI5MDMxMUUxQTExQUM0QzdBNDUyMzhDQyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozQjk4NjhEMTI5MDMxMUUxQTExQUM0QzdBNDUyMzhDQyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjNCOTg2OENFMjkwMzExRTFBMTFBQzRDN0E0NTIzOENDIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjNCOTg2OENGMjkwMzExRTFBMTFBQzRDN0E0NTIzOENDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQABgQEBAUEBgUFBgkGBQYJCwgGBggL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';
	var ImageReelSelectRight = 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAA8AAD/4QMpaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjAtYzA2MCA2MS4xMzQ3NzcsIDIwMTAvMDIvMTItMTc6MzI6MDAgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzUgV2luZG93cyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozMTRDOUJBODI5MDMxMUUxQTRDNkUzOEE3NjQzRTZCMSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozMTRDOUJBOTI5MDMxMUUxQTRDNkUzOEE3NjQzRTZCMSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjMxNEM5QkE2MjkwMzExRTFBNEM2RTM4QTc2NDNFNkIxIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjMxNEM5QkE3MjkwMzExRTFBNEM2RTM4QTc2NDNFNkIxIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQABgQEBAUEBgUFBgkGBQYJCwgGBggL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';
	var ImageReelSelectBoth = 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAA8AAD/4QMpaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjAtYzA2MCA2MS4xMzQ3NzcsIDIwMTAvMDIvMTItMTc6MzI6MDAgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzUgV2luZG93cyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0NTkzMkU5OTI5MDMxMUUxOENCNkVCODVGMjlDMDUyQiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0NTkzMkU5QTI5MDMxMUUxOENCNkVCODVGMjlDMDUyQiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjQ1OTMyRTk3MjkwMzExRTE4Q0I2RUI4NUYyOUMwNTJCIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQ1OTMyRTk4MjkwMzExRTE4Q0I2RUI4NUYyOUMwNTJCIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQABgQEBAUEBgUFBgkGBQYJCwgGBggL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';
	var Image8160 = 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAADAAD/4QMZaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjAtYzA2MCA2MS4xMzQ3NzcsIDIwMTAvMDIvMTItMTc6MzI6MDAgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjNGOTdEQUVFMjgyODExRTFCMjgxOUE4MDcxM0JFRTI1IiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjNGOTdEQUVEMjgyODExRTFCMjgxOUE4MDcxM0JFRTI1IiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzUgV2luZG93cyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJFMzc5MkVCNkE1NzA0NUVBQjE2RTkwRkJFOENCNzgwNyIgc3RSZWY6ZG9jdW1lbnRJRD0iRTM3OTJFQjZBNTcwNDVFQUIxNkU5MEZCRThDQjc4MDciLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAAZFxckGSQ5IiI5Qi8tL0JDODY2OENHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHARskJC8mLzgjIzhHOC44R0dHPj5HR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0f/wAARCABAAJwDASIAAhEBAxEB/8QAbwAAAwEBAQEAAAAAAAAAAAAAAAMEAQIFBgEBAAAAAAAAAAAAAAAAAAAAABAAAQIFAgMGBAYDAAAAAAAAAAECERIDkwRT0yExUkFREzNzs4FEhMTwcZHRIiRDFKQRAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhEDEQA/APoKlV6uWnShMnFyu5Njy5c1XuinDiq8o8yZOpTtu3QxvMreontUylXIgE0mTqU7bt0JMnUp23bo6dTJlAVJk6lO27dCXJT/ACU7bt07jEAFwyNSnadumQyNSnadujYGwATDI1Kdp26Z/Z66dt26OABMcnrp23bpkcnrp23bo8AJpcjrp23boSZHXTtu3SkAJpMjrp23boSZHXTtu3Sk1EiBLJkddO27dCTI66dt26WSKEigSIldvFVY9OlGq1f1Vzk+EOPehR47PC8WP8IR/Cc49kOceEIjEZDmedD+vDs8eH/SBVjeZW9RPaplDmx4oT43mVvUT2qZUAmChBRwAJgoQU1yrE5A2CmAAAAAAAAAAAaBgIsDqCd5v8QO0WKRNMRyGgB5fy/1H3J6h5fy/wBR9yBVjeZW9RPaplRLjeZW9RPaplQAAHLnQA1UiJVIHUynIAAAAAAAAAMa1FSICwHSoEEASA6CGwgAlGqo4AADy/l/qPuT1Dy/l/qPuQKKDpalbvnT26f7KOmU5rUZlnYsr+UYRRU7nJwinHhBUVO/isZ5MjrZbdugVRUwmlyOunbduhJkddO27dApAmkyOunbduhJkddO27dApAmkyOunbdujG0sjmlSnbdugNhE2VRcmTqU7bt0JMnUp23boDkYvaM5EsmTqU7bt0JMnUp23boFQEsmTqU7bt0JMnUp23boFQEsmTqU7bt0JMnUp23boFQEsmTqU7bt0JMnUp23boFR5fy8ezx4/D/ZjH8ocSrwq7uD6jZe2RitX9Ve6HwSPcqcx/hMk8OCSwll7IcofkB//2Q==';
	var Image8164 = 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAPAAD/4QMZaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjAtYzA2MCA2MS4xMzQ3NzcsIDIwMTAvMDIvMTItMTc6MzI6MDAgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkRFQjdEQTREMjgyNzExRTE4REQ0Rjc5N0NDQkM5MDkzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkRFQjdEQTRDMjgyNzExRTE4REQ0Rjc5N0NDQkM5MDkzIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzUgV2luZG93cyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJFMDNEREUzQUNDNzc2NUY3QzIzMDY1NEE4RDk1RDVEQSIgc3RSZWY6ZG9jdW1lbnRJRD0iRTAzRERFM0FDQzc3NjVGN0MyMzA2NTRBOEQ5NUQ1REEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAATDw8XERclFhYlLyQdJC8sJCMjJCw6MjIyMjI6Qz09PT09PUNDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDARQXFx4aHiQYGCQzJB4kM0IzKSkzQkNCPjI+QkNDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0P/wAARCABSAIgDASIAAhEBAxEB/8QAbQABAQEBAQEBAAAAAAAAAAAAAAQDAQIFBgEBAAAAAAAAAAAAAAAAAAAAABAAAQMBBAkCBgMBAAAAAAAAAAECAwQRMXNU0RKSM5Oz0xQVIUFRYYGRIgUyEzRSEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwD9XI+SWRYol1Ub/N9/qtzWp8fdVX0ssSxbVVvjsps1Lsw9M5Rb6pxW8mIuAi7KbNS7MPTHZTZqXZh6ZaAIuymzUuzD0x2U2al2YemWgCLsps1Lsw9MdlNmpdmHploAi7KbNS7MPTHZTZqXZh6ZaAIuymzUuzD0zOSjmRP9MuzD0y18lnohkqqt4EXZy5mX7RdMdnLmZftF0ywAR9nLmZftF0x2cuZl+0XTLABgx8kb/wCuT1a7+D7vVL2r8/dFT0stSxLE1hlWb2mxV5UgApot9VYreTEWkVFvqrFbyYi0AAAAPLnohmsq+wGwtJ1eq+5y0DV0qJcZq9ynkAAAAAOgcO2Ke2Mt9VNgPl1jV/upsVeVIDet31Niu5MgAUW+qsVvJiLSKi31Vit5MZaAMpH+yGplIz3QDIAAAAAAAAA6B1iWqUWHhjNVPmewAAAird9TYruTIBW76mxXcmQAKLfVWK3kxlpDQb2qxk5UZcAF4AE7m6q2Hk0lvMwAAAA6jVW41bFZeBm1iuNmsRp6AAAAAABFW76mxXcmQCt31Niu5MgA5Qb2qxk5UZcQNelJM9XpZHKuvr+yOREaut/yljUsW6+2z8db15ehzEXEbpAtBF5ehzEXEbpHl6HMRcRukDeW8zJpP21Eq/6IuI3Scb+zoVvqIuK3SBWiWmjYviSt/a0DbqiLit0nry9DmIuK3SBYiIlx0i8vQ5iLit0jy9DmIuK3SBaCLy9DmIuK3SPL0OYi4rdIFoIvL0OYi4rdI8vQ5iLit0gWgi8vQ5iLit0jy9DmIuK3SArd9TYruTIBrJVyscxLY4lV6PuRXK1W/j8U1XLat1tiJautqgNV+hwAAAANIvoagAAAAAAAAAAAACgAcUAAf//Z';
	dymo.label.framework.FlowDirection = 'LeftToRight';

	var PageOne = $('<table width="100%" cellspacing="20">' +
		'<tbody>' +
		'<tr>' +
		($.inArray('8160-b', LabelPrinter.option('labelTypes')) > -1 ? '<td data-label_type="8160-b" align="center" class="ui-corner-all" style="border:2px solid white;"><img src="' + Image8160 + '"><br>Address<br>( For: Barcode )</td>' : '') +
		($.inArray('8160-s', LabelPrinter.option('labelTypes')) > -1 ? '<td data-label_type="8160-s" align="center" class="ui-corner-all" style="border:2px solid white;"><img src="' + Image8160 + '"><br>Address<br>( For: Shipping )</td>' : '') +
		($.inArray('8164', LabelPrinter.option('labelTypes')) > -1 ? '<td data-label_type="8164" align="center" class="ui-corner-all" style="border:2px solid white;"><img src="' + Image8164 + '"><br>Shipping<br>( For: Product Info )</td>' : '') +
		'</tr>' +
		'</tbody>' +
		'</table>');

	var PageTwo = $('<table>' +
		'<tbody></tbody>' +
		'</table>');

	var PageThree = $('<table>' +
		'<tbody></tbody>' +
		'</table>');

	var PageFour = $('<div style=""></div>');

	this.isAllowed = function () {
		var allowDymo = true;
		if (dymo){
			var dymoCheck = dymo.label.framework.checkEnvironment();
			if (dymoCheck.isBrowserSupported === false){
				allowDymo = false;
			}
			if (dymoCheck.isFrameworkInstalled === false){
				allowDymo = false;
			}

			if (allowDymo === true){
				var Printers = dymo.label.framework.getPrinters();
				allowDymo = (Printers.length > 0);
				if (allowDymo === true){
					allowDymo = false;
					$.each(Printers, function (){
						if (this.isConnected){
							allowDymo = true;
						}
					});
				}
			}
		}
		return allowDymo;
	};

	this.addSplashImage = function (ListItem) {
		ListItem.append('<img src="' + splashImage + '"><br>DYMO Printer');
	};

	this.load = function () {
		this.loadPageOne();
	};

	this.loadPageTwo = function (isBack) {
		LabelPrinter.setDialogTitle('Select Label Type');
		LabelPrinter.setDialogBody(PageOne);

		PageOne.find('td').each(function () {
			$(this)
				.mouseover(function () {
					$(this).css({
						'cursor' : 'pointer',
						'border-color' : '#ccc'
					});
				})
				.mouseout(function () {
					$(this).css({
						'cursor' : 'default',
						'border-color' : '#fff'
					});
				})
				.click(function () {
					$(this).css({
						'cursor' : 'default',
						'border-color' : '#fff'
					});

					var labelType = $(this).data('label_type');
					if (SelectedPrinter.printerType == 'TapePrinter'){
						labelType += '-tape';
					}
					LabelPrinter.setUserData('labelType', labelType);
					mainSelf.loadPageThree();
				});
		});

		LabelPrinter.setDialogButtons({
			'Back' : function () {
				LabelPrinter.loadSplash(true);
			}
		});
	};

	this.loadPageOne = function (isBack) {
		var Printers = dymo.label.framework.getPrinters();
		if (Printers.length > 1){
			LabelPrinter.setDialogTitle('Select Printer');
			LabelPrinter.setDialogBody(PageTwo);
			PageTwo.find('tbody').empty();
			$.each(Printers, function () {
				var input = $('<input type="radio" name="printerName" value="' + this.name + '" />')
					.data('printerInfo', this);

				var td1 = $('<td></td>')
					.append(input);
				var td2 = $('<td></td>')
					.html(this.name);

				var newTr = $('<tr></tr>')
					.append(td1)
					.append(td2);

				PageTwo.find('tbody').append(newTr);
			});

			LabelPrinter.setDialogButtons({
				'Back' : function () {
					mainSelf.loadPageOne(true);
				},
				'Select Printer': function (){
					SelectedPrinter = $('input[name=printerName]:checked').data('printerInfo');
					mainSelf.loadPageTwo();
				}
			});
		}
		else {
			if (isBack){
				this.loadPageOne(true);
			}
			else {
				SelectedPrinter = Printers[0];
				this.loadPageThree();
			}
		}
	};

	this.loadPageThree = function (isBack) {
		LabelPrinter.setDialogTitle('Configure Print Settings');
		LabelPrinter.setDialogBody(PageThree);

		PageThree.find('tbody').empty();
		PageThree.find('tbody').append('<tr>' +
			'<td>Printer: </td>' +
			'<td>' + SelectedPrinter.name + '</td>' +
			'</tr>');

		if (SelectedPrinter.isTwinTurbo){
			PageThree.find('tbody').append('<tr>' +
				'<td>Select Reel: </td>' +
				'<td>' +
				'<img data-reel="Left" class="twinTurboReelSelect" src="' + ImageReelSelectLeft + '" style="border:2px solid white">&nbsp;' +
				'<img data-reel="Both" class="twinTurboReelSelect" src="' + ImageReelSelectBoth + '" style="border:2px solid white">&nbsp;' +
				'<img data-reel="Right" class="twinTurboReelSelect" src="' + ImageReelSelectRight + '" style="border:2px solid white">' +
				'</td>' +
				'</tr>');

			PageThree.find('tbody .twinTurboReelSelect').each(function () {
				$(this)
					.mouseover(function () {
						$(this).addClass('ui-state-hover');
						$(this).css({
							'cursor' : 'pointer',
							'border-color' : '#ccc'
						});
					})
					.mouseout(function () {
						if (!$(this).hasClass('ui-state-selected')){
							$(this).removeClass('ui-state-hover');
							$(this).css({
								'cursor' : 'pointer',
								'border-color' : '#fff'
							});
						}
					})
					.click(function () {
						$('.twinTurboReelSelect.ui-state-selected')
							.css('border-color', '#fff')
							.removeClass('ui-state-selected');

						$(this).addClass('ui-state-selected');

						LabelPrinter.setUserData('selectedReel', $(this).data('reel'));
					});
			});
		}else if (SelectedPrinter.printerType == 'TapePrinter'){
			PageThree.find('tbody').append('<tr>' +
				'<td>Tape Cut Mode: </td>' +
				'<td>' +
				'<input type="radio" value="AutoCut" name="TapeCutMode" checked="checked">AutoCut<br>' +
				'<input type="radio" value="ChainMarks" name="TapeCutMode">ChainMarks<br>' +
				'</td>' +
				'</tr>');
			PageThree.find('tbody').append('<tr>' +
				'<td>Tape Alignment: </td>' +
				'<td>' +
				'<input type="radio" value="Left" name="TapeAlignment" checked="checked">Left<br>' +
				'<input type="radio" value="Center" name="TapeAlignment">Center<br>' +
				'<input type="radio" value="Right" name="TapeAlignment">Right<br>' +
				'</td>' +
				'</tr>');
		}

		LabelPrinter.setDialogButtons({
			'Back' : function () {
				mainSelf.loadPageTwo(true);
			},
			'Preview Labels' : function () {
				PageThree.find('input[type=radio]:checked, select').each(function () {
					LabelPrinter.setUserData($(this).attr('name'), $(this).val());
				});

				mainSelf.loadPageFour();
			}
		});
	};

	this.loadPageFour = function (isBack) {
		LabelPrinter.setDialogTitle('Preview Labels');
		LabelPrinter.setDialogBody(PageFour);

		$.ajax({
			cache : false,
			url : LabelPrinter.option('printUrl'),
			dataType : 'json',
			data : LabelPrinter.GetPrintData(),
			type : 'get',
			success : function (resp) {
				if (SelectedPrinter.printerType == 'TapePrinter'){
					dymo.label.framework.TapeCutMode = LabelPrinter.getUserData('TapeCutMode');
					dymo.label.framework.TapeAlignment = LabelPrinter.getUserData('TapeAlignment');
				}
				var label = dymo.label.framework.openLabelXml(resp.labelInfo.xmlData);
				var previewImages = [];
				var renderParams = dymo.label.framework.createLabelRenderParamsXml({
					labelColor : {
						a : 255,
						r : 255,
						g : 255,
						b : 255
					},
					shadowColor : {
						a : 255,
						r : 204,
						g : 204,
						b : 204
					},
					shadowDepth : 50,
					flowDirection : dymo.label.framework.FlowDirection,
					pngUseDisplayResolution : true
				});

				$.each(resp.labelInfo.data, function () {
					$.each(this, function (k, v) {
						if (k == 'BarcodeType'){
							label.setBarcodeType("Barcode", v);
						}
						else {
							label.setObjectText(k, v);
						}
					});

					previewImages.push({
						labelXml : label.getLabelXml(),
						image : this.Barcode/*'<img width="175" src="data:image/png;base64,' + label.render(renderParams, SelectedPrinter.name) + '"/>'*/
					});
				});

				var TEXT_BUTTON_PRINT_NO = 'Don\'t Print';
				var TEXT_BUTTON_PRINT_YES = 'Print';
				var TEXT_BUTTON_ZOOM = 'Zoom In';
				var TEXT_BUTTON_UNZOOM = 'Zoom Out';
				
				PageFour.empty();
				$.each(previewImages, function (k, v) {
					PageFour.append('<div style="float:left;text-align:center;margin-bottom:15px;margin-left:5px;font-size: 0.8em;">' +
						this.image +
						'<br>' +
						'<button class="printStatusButton" data-preview_index="' + k + '">' +
						'<span>' + TEXT_BUTTON_PRINT_NO + '</span>' +
						'</button>' +
						'<button class="zoomButton">' +
						'<span>' + TEXT_BUTTON_ZOOM + '</span>' +
						'</button>' +
						'</div>');
				});

				PageFour.find('button').button();
				PageFour.find('.printStatusButton').click(function () {
					var image = $(this).parent().find('img');
					if (image.hasClass('ui-state-disabled')){
						image.removeClass('ui-state-disabled');
						$(this).parent().find('.zoomButton').button('enable');
						$(this).find('span').html(TEXT_BUTTON_PRINT_NO);
					}
					else {
						image.addClass('ui-state-disabled');
						$(this).parent().find('.zoomButton').button('disable');
						$(this).find('span').html(TEXT_BUTTON_PRINT_YES);
					}
				});

				PageFour.find('.zoomButton').click(function (e) {
					if ($(this).hasClass('zoomed')){
						$(this).removeClass('zoomed');
						$(this).parent().find('.printStatusButton').button('enable');
						$(this).find('span').html(TEXT_BUTTON_ZOOM);
						$(this).data('clonedImg').animate({
							width : $(this).data('original_width'),
							height : $(this).data('original_height'),
							top : $(this).data('original_top'),
							left : $(this).data('original_left')
						}, {
							duration : 1000,
							complete : function () {
								$(this).remove();
							}
						});
					}
					else {
						var image = $(this).parent().find('img');
						$(this).parent().find('.printStatusButton').button('disable');
						$(this).addClass('zoomed');
						$(this).find('span').html(TEXT_BUTTON_UNZOOM);
						var cloneImg = image.clone();
						var zoomAddWidth = (image.width() * 1);
						var zoomAddHeight = (image.height() * 1);
						cloneImg.css({
							position : 'absolute',
							left : image.offset().left,
							top : image.offset().top,
							zIndex : 9999
						}).appendTo(document.body).animate({
								width : image.width() + zoomAddWidth,
								height : image.height() + zoomAddHeight,
								top : image.offset().top - zoomAddHeight,
								left : image.offset().left - zoomAddWidth
							}, {
								duration : 1000,
								complete : function () {
									$(document.body).one('click', function () {
										$('.zoomed').click();
									});
								}
							});
						$(this).data('clonedImg', cloneImg);
						$(this).data('original_width', image.width());
						$(this).data('original_height', image.height());
						$(this).data('original_top', image.offset().top);
						$(this).data('original_left', image.offset().left);
					}
					return false;
				});

				LabelPrinter.setDialogButtons({
					'Back' : function () {
						mainSelf.loadPageThree(true);
					},
					'Print Labels' : function () {
						mainSelf.printLabels(previewImages);
					}
				});
			}
		});
	};

	this.printLabels = function (labels) {
		if (SelectedPrinter.printerType == 'TapePrinter'){
			var printParams = dymo.label.framework.createTapePrintParamsXml({
				copies : 1,
				jobTitle : 'Label Printer',
				flowDirection : dymo.label.framework.FlowDirection,
				alignment: dymo.label.framework.TapeAlignment,
				cutMode: dymo.label.framework.TapeCutMode
			});
		}else{
			var printParams = dymo.label.framework.createLabelWriterPrintParamsXml({
				copies : 1,
				jobTitle : 'Label Printer',
				flowDirection : dymo.label.framework.FlowDirection,
				printQuality : 'BarcodeAndGraphics',
				twinTurboRoll : LabelPrinter.getUserData('selectedReel')
			});
		}

		PageFour.find('.printStatusButton').each(function (){
			var image = $(this).parent().find('img');
			if (image.hasClass('ui-state-disabled') === false){
				dymo.label.framework.printLabel(
					SelectedPrinter.name,
					printParams,
					labels[$(this).data('preview_index')].labelXml
				);
			}
		});
	};
};
