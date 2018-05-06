<template>
    <div>
        <div class="alert alert-success" v-if="success">{{ success }}</div>
        <div class="alert alert-danger" v-if="error">{{ error }}</div>
	<div class="pull-left">
            <h5>Create shapes using Google Polygon</h5>
            <GmapMap
                :center="{lat:10, lng:10}"
		:zoom="7"
		ref="mapRef"
		map-type-id="terrain"
		style="width: 500px; height: 300px">
            <GmapMarker
                :key="index"
		v-for="(m, index) in markers"
		:position="m.position"
		:clickable="true"
		:draggable="true"
		@click="center=m.position"/>
            </GmapMap>
	</div>
	<div class="pull-left" style="padding: 15px;" v-if="loaded">
            <div class="dash-map-button-wrapper pull-left" v-if="this.polygonList.length > 0">
                <p>Max perimeter: {{ (maxPerimeter / 1000 ).toFixed(2) }}km</p>
		<div class="btn-group-vertical" role="group">
                    <button class="btn btn-default" 
                        v-for="(value, key) in polygonList"
			v-on:click="showShape(key, value.overlay)">shape #{{ key + 1 }}</button>
                    <button class="btn btn-danger" v-on:click="clearList()">Clear all</button>
				
		</div>
            </div>
            <div class="dash-map-no-data" v-else>No data</div>
	</div>
        <div v-else>
            Loading shapes...
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                polygonList: [],
                maxPerimeter: 0,
		markers: null,
		error: false,
		success: false,
                loaded: false
            };
	},
	mounted() {
            this.$refs.mapRef.$mapPromise.then(function (map) {
            map.panTo({lat: 48.471064, lng: 35.003075});

                var drawingManager = new google.maps.drawing.DrawingManager({
                    drawingMode: google.maps.drawing.OverlayType.POLYGON,
                    drawingControl: true,
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
			drawingModes: ['polygon']
                    }
		});
				
		google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
                    if (event.type == 'polygon') {
                        this.resetMaxPerimeter(event.overlay);
			this.polygonList.forEach(function(item, index) {
                            item.overlay.setVisible(false);
			});
						
			this.saveShape(event.overlay);
                    }
		}.bind(this));
				
		drawingManager.setMap(map);

		this.map = map;
		this.loadUserShapes();
            }.bind(this));
	},
        methods: {
            clearList() {
                var item = null,
                    i, index;
		for (i = 0; i < this.polygonList.length; i++) {
                    index = i;
                    item = this.polygonList[index];
                    axios.delete('/api/shapes/' + item.sourceId)
                        .then( function (response) {
                            this.list.splice(0,1);
                            this.item.overlay.setMap(null);
                        }.bind({index, item, list: this.polygonList}))
                        .catch( (error) =>  { this.error = error.response.data.message; });
				}
            },
            calculatePerimeter(overlay) {
                return google.maps.geometry
                    .spherical
                    .computeLength(overlay.getPath().getArray());
            },
            resetMaxPerimeter(overlay) {
                var perimeter = this.calculatePerimeter(overlay);
						
                if (perimeter > this.maxPerimeter) {
                    this.maxPerimeter = perimeter;
                }
            },
            loadUserShapes() {
                axios.get('/api/shapes')
                    .then(function (response) {
                        var shapes = response.data.shapes,
                            shapesCount = shapes.length,
                            i = 0,
                            visible = true;

			for (i; i < shapesCount; i++) {
                            var itemShape = shapes[i],
                                shapeCoords = itemShape.coordinates,
				itemPolygon = new google.maps.Polygon({
                                    paths: shapeCoords,
                                    visible: visible
				});

                            itemPolygon.setMap(this.map);

                            this.resetMaxPerimeter(itemPolygon);
                            this.polygonList.push({overlay: itemPolygon, sourceId: itemShape.id});

                            visible = false;
                        }

                        this.loaded = true;
                    }.bind(this))
		.catch((error) => { this.error = error.response.data.message; });
            },
            showShape(k,shape) {
                this.polygonList.forEach(function (item, index) {
                    item.overlay.setVisible(index === this.k);
		}.bind({k,shape}));
            },
            saveShape(shape) {
                axios.post('/api/shapes', {
                    body: {
                        coordinates: shape.getPath().getArray()
                    }
		})
		.then(function (response) {
                    this.polygonList.push({overlay: shape, sourceId: response.data.shape.id});
                    this.error = false;
		}.bind(this))
		.catch(function (error) {
                    this.error = error.response.data.message;
		}.bind(this));
            }
	}
    }
</script>
