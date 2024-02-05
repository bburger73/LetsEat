import React from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { ActivityIndicator, KeyboardAvoidingView, BackHandler, SafeAreaView, StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Platform } from "react-native";
import { useToast } from "react-native-toast-notifications";
import Button from './button';
import './global.js';

export default function CreateFeastGroup(props) {
    useFocusEffect(
        React.useCallback(() => {
            const onBackPress = () => {
                goback();
                return true;
            };

            BackHandler.addEventListener(
                'hardwareBackPress', onBackPress
            );

            return () =>
                BackHandler.removeEventListener(
                    'hardwareBackPress', onBackPress
                );
        }, [])
    );


    const styles = StyleSheet.create(
        Platform.select({
            ios: global.mainstyles,
            android: global.mainstyles,
            default: global.mainstyles
        })
    );


    const [name, setName] = React.useState(props.user.name);
    const toast = useToast();
    const [isLoadingName, setLoadingName] = React.useState(false);


    const confirmUpdateName = () => {
        setLoadingName(true);
        if (name !== null && name !== undefined && name !== "") {
            console.log(props.user.user_token);
            const requestOptions = {
                method: "POST",
                headers: {
                    "LETSEAT-AUTHKEY": props.user.user_token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    name: name
                }),
            };

            fetch(global.server + "feast_group/create.php", requestOptions).then(res => res.json()).then(result => {
                if (result.result) {
                    alert("Group Created Successfully!");
                    setLoadingName(false);
                    props.pagging(12);
                } else {
                    alert("Group Not Created");
                }
            }).catch(() => {
                setTimeout(() => {
                    setLoadingName(false);
                    alert("Failed To Connect To Server");
                }, 2000);
            }).finally(() => {
                setTimeout(() => setLoadingName(false), 2000);
            });
        } else {
            toast.show("Please enter text into the field", {
                type: "warning",
                placement: "top",
                duration: 4000,
                offset: 100,
                animationType: "slide-in",
            });
            setLoadingName(false);
        }
    }

    const goback = () => {
        props.pagging(12)
    }

    return (
        <SafeAreaView>
            <View style={styles.main}>
                <View style={styles.navbar}>
                    <View style={styles.backbuttonview}>
                        <Button
                            textstyle={styles.backbutton}
                            title="Back"
                            onPress={goback}>
                        </Button>
                    </View>
                </View>
                <View style={styles.scrollView}>
                    <KeyboardAvoidingView
                        behavior={Platform.OS === "ios" ? "height" : "padding"}
                        enabled>
                        <ScrollView>
                            <View
                                style={styles.signin}
                            >
                                <Text style={styles.dashboardheader}>
                                    Create Feast Group
                                </Text>
                                <Text>Group Name</Text>
                                <TextInput
                                    style={styles.input}
                                    onChangeText={setName}
                                    value={name}
                                    placeholder="Doe Family Dinner"
                                    onSubmitEditing={() => {
                                        confirmUpdateName();
                                    }}
                                />
                                <Button
                                    onPress={() => {
                                        confirmUpdateName();
                                    }}
                                    title="Create Group"
                                    // textstyle={styles.button}
                                    textstyle={styles.topbutton}
                                    accessibilityLabel="Create Group"
                                    isLoading={isLoadingName}
                                />
                            </View>
                        </ScrollView>
                    </KeyboardAvoidingView>
                </View>
            </View>
        </SafeAreaView>

    )
}