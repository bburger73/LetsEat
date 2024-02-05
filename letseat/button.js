import React from 'react';
import { ActivityIndicator, View, Text, TouchableOpacity } from "react-native";

export default function Button(props) {
    return (
        <TouchableOpacity onPress={() => props.onPress()}>
            {(props.isLoading)?
                <View style={props.textstyle}>
                    <ActivityIndicator color={props.textstyle.color} size="large" />
                </View>:
                <Text style={props.textstyle}>{props.title}</Text> 
            }
        </TouchableOpacity>
    )
}